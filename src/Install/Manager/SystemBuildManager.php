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
	 * DatabaseManager constructor.
	 *
	 * @param EntityManagerInterface       $entityManager
	 * @param SettingManager               $settingManager
	 * @param UserPasswordEncoderInterface $encoder
	 */
	public function __construct(EntityManagerInterface $entityManager, SettingManager $settingManager, ContainerInterface $container, PasswordManager $passwordManager)
	{
		$this->entityManager = $entityManager;
		$this->messages = new MessageManager('Install');
		$this->settingManager = $settingManager;
		$this->passwordManager = $passwordManager;
		parent::__construct($container->getParameter('kernel.project_dir'));
	}

	/**
	 * Build Database
	 *
	 * @version 30th August 2017
	 * @since   23rd October 2016
	 *
	 * @throws DriverException
	 * @return  void
	 */
	public function buildDatabase()
	{
		$conn = $this->entityManager->getConnection();

		$conn->exec('SET FOREIGN_KEY_CHECKS = 0');

		$schemaTool = new SchemaTool($this->entityManager);

		$metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();

		$xx = $schemaTool->getUpdateSchemaSql($metaData, true);

		$count = count($xx);
		$this->addMessage('info', 'system.build.database.count', ['%count%' => $count]);

		$ok = true;
		foreach ($xx as $sql) {
			try
			{
				$conn->executeQuery($sql);
			} catch (DriverException $e){
				if ($e->getErrorCode() == '1823')
				{
					$ok = false;
					$this->addMessage('danger', 'system.build.database.error', ['%error%' => $e->getMessage()]);
					dump($e);
				}
			}
		}

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
	 * @param $data
	 */
	public function buildSystemSettings()
	{
		if ($this->getSettingManager()->has('version'))
			$version = $this->getSettingManager()->get('version', '0.0.00');
		else
			$version = '0.0.00';

		$installed = $version;
		$software = VersionManager::VERSION;

		$this->systemSettingsInstalled = true;

		if (version_compare($installed, $software, '>='))
			return true;

		$this->systemSettingsInstalled = false;

		$list = VersionManager::listSettings();

		while (version_compare($installed, $software, '<'))
		{
			foreach($list as $version=>$class)
			{
				if (! $class instanceof SettingInterface)
					trigger_error('The setting class '.$version.' is not correctly formated as a SettingInterface.');

				if (version_compare($installed, $version, '<'))
				{
					$data = Yaml::parse($class->getSettings());

					foreach ($data as $name => $datum)
					{
						$entity = $this->settingManager->getSettingEntity($name);
						if (!$entity instanceof Setting)
						{
							$entity = new Setting();
							if (empty($datum['type']))
								trigger_error('When creating a setting the type must be defined. ' . $name);
							$entity->setType($datum['type']);
						}
						$entity->setName($name);
						foreach ($datum as $field => $value)
						{
							$w = 'set' . ucwords($field);
							$entity->$w($value);
						}
						$this->settingManager->createSetting($entity);
					}
				}
				$this->messages->add('success', 'install.system.setting.file', ['%{class}' => $class->getClassName()]);
				$installed = $version;
			}

			if (version_compare($installed, $software, '='))
			{
				$this->systemSettingsInstalled = true;
				sleep(1);//$this->getSettingManager()->set('version', $installed);
			}elseif (version_compare($installed, $software, '<'))
				trigger_error('You need to supply a setting class for version '. $software);
			elseif (version_compare($installed, $software, '>'))
				trigger_error('The setting class is trying to install a version ('.$installed.') greater than the software version ('.$software.').');
		}

		return false;
	}

	/**
	 * @param string $projectDir
	 * @param array  $data
	 *
	 * @return void
	 */
	public function writeSystemUser(string $projectDir, array $userParams = [])
	{
		$this->projectDir = $projectDir;

		$user = $this->entityManager->getRepository(User::class)->find(1);

		if (empty($userParams) || empty($userParams['_username']))
			return ;

		if (! $user instanceof User)
			$user = new User();

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
		$user->setUserSetting('Calendar', $cal, 'object');

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
}