<?php
namespace App\Install\Manager;

use App\Core\Definition\SettingInterface;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Setting;
use Hillrange\Security\Entity\User;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

class SystemBuildManager
{
	/**
	 * @var EntityManager
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
	 * @var TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * @var string
	 */
	private $projectDir;

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
	public function __construct(EntityManagerInterface $entityManager, SettingManager $settingManager, UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage)
	{
		$this->entityManager = $entityManager;
		$this->messages = new MessageManager('Install');
		$this->settingManager = $settingManager;
		$this->encoder = $encoder;
		$this->tokenStorage = $tokenStorage;
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
dump(version_compare($installed, $software, '<'));
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


		$this->addMessage('success', 'bundle.update.resource.success', ['%resource%' => $resource]);
	}

	/**
	 * @param string $projectDir
	 *
	 * @return void
	 */
	public function writeSystemUser(string $projectDir)
	{
		$this->projectDir = $projectDir; dump($this);
		$params = Yaml::parse(file_get_contents($projectDir.'/config/packages/busybee.yaml'));

		$user = $this->entityManager->getRepository(User::class)->find(1);

		if (! isset($params['parameters']['user_name']))
			return ;

		if (! $user instanceof User)
			$user = new User();

		$user->setInstaller(true);
		$user->setUsername($params['parameters']['user_name']);
		$user->setUsernameCanonical($params['parameters']['user_name']);
		$user->setEmail($params['parameters']['user_email']);
		$user->setEmailCanonical($params['parameters']['user_email']);
		$user->setLocale('en');
		$user->setLocked(false);
		$user->setExpired(false);
		$user->setCredentialsExpired(false);
		$user->setEnabled(true);
		$user->setDirectroles(['ROLE_SYSTEM_ADMIN']);
		$password = $this->encoder->encodePassword($user, $params['parameters']['user_password']);
		$user->setPassword($password);

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		unset($params['parameters']['user_name'], $params['parameters']['user_password'], $params['parameters']['user_email']);

		file_put_contents($this->projectDir.'/config/packages/busybee.yaml', Yaml::dump($params));

		$token = new UsernamePasswordToken($user, null, "default", $user->getRoles());

		$this->tokenStorage->setToken($token);

		return ;
	}

	/**
	 * @return bool
	 */
	public function isSystemSettingsInstalled(): bool
	{
		return $this->systemSettingsInstalled;
	}
}