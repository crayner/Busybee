<?php
namespace App\Install\Manager;

use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Tools\SchemaTool;

class SystemBuildManager
{
	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	/**
	 * @var MessageManager
	 */
	private $messages;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var array
	 */
	private $parameters;

	/**
	 * DatabaseManager constructor.
	 *
	 * @param ObjectManager $objectManager
	 */
	public function __construct(ObjectManager $objectManager, SettingManager $settingManager)
	{
		$this->objectManager = $objectManager;
		$this->messages = new MessageManager('Install');
		$this->settingManager = $settingManager;
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
		$conn = $this->objectManager->getConnection();

		$conn->exec('SET FOREIGN_KEY_CHECKS = 0');

		$schemaTool = new SchemaTool($this->objectManager);

		$metaData = $this->objectManager->getMetadataFactory()->getAllMetadata();

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
}