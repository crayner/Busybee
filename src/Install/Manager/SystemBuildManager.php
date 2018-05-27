<?php
namespace App\Install\Manager;

use App\Core\Definition\SettingInterface;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Calendar;
use App\Entity\Setting;
use Hillrange\Security\Entity\User;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Hillrange\Security\Util\ParameterInjector;
use Hillrange\Security\Util\PasswordManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

class SystemBuildManager extends InstallManager
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var MessageManager
	 */
	private $messages;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var PasswordManager
	 */
	private $passwordManager;

	/**
	 * @var bool
	 */
	private $systemSettingsInstalled = false;

	/**
	 * @var bool
	 */
	private $action = false;

    /**
     * @var ParameterInjector
     */
	private $parameterInjector;
	/**
	 * DatabaseManager constructor.
	 *
	 * @param EntityManagerInterface       $entityManager
	 * @param SettingManager               $settingManager
	 * @param UserPasswordEncoderInterface $encoder
	 */
	public function __construct(EntityManagerInterface $entityManager, SettingManager $settingManager, ContainerInterface $container, PasswordManager $passwordManager, ParameterInjector $parameterInjector)
	{
		$this->entityManager = $entityManager;
		$this->messages = new MessageManager('Install');
		$this->settingManager = $settingManager;
		$this->passwordManager = $passwordManager;
        $this->parameterInjector = $parameterInjector;
		parent::__construct($parameterInjector->getParameter('kernel.project_dir'));
	}

	/**
	 * Build Database
	 * @version 30th August 2017
	 * @since   23rd October 2016
	 */
	public function buildDatabase(): bool
	{
		$conn = $this->entityManager->getConnection();

		if ($this->isAction())
			$conn->exec('SET FOREIGN_KEY_CHECKS = 0');

		$schemaTool = new SchemaTool($this->entityManager);

		$metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();

		$xx = $schemaTool->getUpdateSchemaSql($metaData);

		$count = count($xx);
		if (! $this->isAction())
		{
			$this->addMessage('info', 'system.build.database.count', ['%count%' => $count, '%{software}' => VersionManager::VERSION]);
			return true;
		}
		else
			$this->addMessage('info', 'system.build.database.done', ['%count%' => $count]);

		$ok = true;

		foreach ($xx as $sql) {
			try
			{
				if ($this->isAction())
					$conn->executeQuery($sql);
			} catch (\Exception $e){
				if ($e->getPrevious() instanceof DriverException && $e->getErrorCode() == '1823')
				{
					$ok = false;
					$this->addMessage('danger', 'system.build.database.error', ['%error%' => $e->getMessage()]);
				}
			}
		}
		if ($this->isAction())
			$conn->exec('SET FOREIGN_KEY_CHECKS = 1');

		if ($ok)
			$this->addMessage('success', 'system.build.database.success', ['%count%' => $count]);
		else
			$this->addMessage('warning', 'system.build.database.warning', ['%count%' => $count]);

		return $ok;
	}

	/**
	 * Add Message
	 *
	 * @param       $level
	 * @param       $message
	 * @param array $options
	 *
	 * @return $this
	 */
	private function addMessage($level, $message, $options = [])
	{
		$this->messages->add($level, $message, $options, 'Install');

		return $this;
	}

	/**
	 * @return MessageManager
	 */
	public function getMessages(): MessageManager
	{
		return $this->messages;
	}

	/**
	 * @return SettingManager
	 */
	public function getSettingManager(): SettingManager
	{
		return $this->settingManager;
	}

    /**
     * @return bool
     * @throws \Exception
     */
    public function buildSystemSettings()
	{
	    $current = $this->getSystemVersion();

		$software = VersionManager::VERSION;

		$this->systemSettingsInstalled = true;

		if (version_compare($current, $software, '>='))
			return true;

		$this->systemSettingsInstalled = false;

		if (! $this->isAction())
		{
			$this->messages->add('info', 'update.setting.required', ['%{software}' => $software, '%{current}' => $current]);
			return false;
		}


        while (version_compare($current, $software, '<'))
		{
            $current = VersionManager::incrementVersion($current);

		    $class = 'App\\Core\\Settings\\Settings_' . str_replace('.', '_', $current);

			if (class_exists($class))
			{
			    $class = new $class();

                if (!$class instanceof SettingInterface)
                    trigger_error('The setting class ' . $class->getClassName() . ' is not correctly formatted as a SettingInterface.');

                $data = Yaml::parse($class->getSettings());

                if (isset($data['version']))
                    unset($data['version']);

                $count = $this->settingManager->createSettings($data);
                $this->messages->add('success', 'install.system.setting.file', ['transChoice' => $count, '%{class}' => $class->getClassName()]);
			} else {
                $this->messages->add('info', 'install.system.version.updated', ['%{version}' => $current]);
            }

			if (version_compare($current, $software, '='))
				$this->systemSettingsInstalled = true;
        }

        $this->updateCurrentVersion($current);

		return false;
	}

    /**
     * @param array $userParams
     */
    public function writeSystemUser(array $userParams = [
        '_username' => 'admin',
        '_password' => 'pass_word',
        '_email' => 'no@no_domain.com.nz',
    ])
	{
		$user = $this->entityManager->getRepository(User::class)->find(1);

		if (! $user instanceof User)
			$user = new User($this->parameterInjector);

		$user->setInstaller(true);
		$user->setUsername($userParams['_username']);
		$user->setUsernameCanonical($userParams['_username']);
		$user->setEmail($userParams['_email']);
		$user->setEmailCanonical($userParams['_email']);
		$user->setLocale('en');
		$user->setExpired(false);
		$user->setCredentialsExpired(false);
		$user->setEnabled(true);
		$user->setDirectroles(['ROLE_SYSTEM_ADMIN']);
		$password = $this->passwordManager->encodePassword($user, $userParams['_password']);
		$user->setPassword($password);

		$this->entityManager->persist($user);
		$this->entityManager->flush();


		$cal = $this->entityManager->getRepository(Calendar::class)->findOneByName(date('Y'));

		if (empty($cal))
		{
			$cal = new Calendar();

			$cal->setName(date('Y'));
			$cal->setFirstDay(new \DateTime($cal->getName() . '-01-01'));
			$cal->setLastDay(new \DateTime($cal->getName() . '-12-31'));
			$cal->setStatus('current');
			$cal->setCreatedBy($user);
			$cal->setModifiedBy($user);
			$this->entityManager->persist($cal);
			$this->entityManager->flush();
		}

		$user->setCreatedBy($user);
		$user->setModifiedBy($user);

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		return ;
	}

	/**
	 * @return bool
	 */
	public function isSystemSettingsInstalled(): bool
	{
		return $this->systemSettingsInstalled;
	}

	/**
	 * @return bool
	 */
	public function isUserDefined()
	{
		$user = $this->entityManager->getRepository(User::class)->find(1);

		if ($user)
			return true;

		return false;
	}

	/**
	 * @param Request $request
	 * @param Form    $form
	 */
	public function handleUserParameters(Request $request, Form $form)
	{
		$form->handleRequest($request);
	}

	/**
	 * @param null $name
	 *
	 * @return array|bool|string
	 */
	public function getPasswordSetting($name = null)
	{
		return $this->passwordManager->getPasswordSetting($name);
	}

	/**
	 * @param bool $generate
	 *
	 * @return string
	 */
	public function generatePassword($generate = false)
	{
		return $this->passwordManager->generatePassword($generate);
	}

	/**
	 * @return bool
	 */
	public function isAction(): bool
	{
		return $this->action;
	}

	/**
	 * @param bool $action
	 *
	 * @return SystemBuildManager
	 */
	public function setAction(bool $action): SystemBuildManager
	{
		$this->action = $action;

		return $this;
    }

    /**
     * @return string
     */
    public function getSystemVersion(): string
    {
        if ($this->getSettingManager()->has('version'))
            return $this->getSettingManager()->get('version', '0.0.00');
        else
            return '0.0.00';
    }

    /**
     * @param $current
     */
    private function updateCurrentVersion($current)
    {
        $version = [];
        $data = [];
        $version['type'] = 'system';
        $version['displayName'] = 'System Version';
        $version['description'] = 'The version of Busybee currently configured on your system.';
        $version['role'] = 'ROLE_SYSTEM_ADMIN';
        $version['value'] = $current;
        $version['defaultValue'] = '0.0.00';
        $data['version'] = $version;
        $this->getSettingManager()->createSettings($data);
    }
}