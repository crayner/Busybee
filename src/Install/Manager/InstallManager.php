<?php
namespace App\Install\Manager;

use App\Core\Organism\Database;
use Doctrine\DBAL\Exception\SyntaxErrorException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class InstallManager
{
	/**
	 * @var Database
	 */
	private $sql;

	/**
	 * @var string
	 */
	private $projectDir;

	/**
	 * InstallManager constructor.
	 *
	 * @param $projectDir   String
	 */
	public function __construct($projectDir)
	{
		$this->sql             = new Database();
		$this->projectDir = $projectDir;
	}

	/**
	 * Get SQL Parameters
	 *
	 * @param array $params
	 *
	 * @return Database
	 */
	public function getSQLParameters(): Database
	{
		$params = file($this->projectDir . '/.env');

		$x = Yaml::parse(file_get_contents($this->projectDir . '/config/packages/doctrine.yaml'));
		$x = $x['parameters'];

		$params = array_merge($params, $x);

		foreach ($params as $name => $value)
			if(strpos($name,'db_') === 0)
				$params['parameters'][str_replace('db_', '', $name)] = $value;

		$params['parameters'] = array_merge($params['parameters']);

		foreach($params['parameters'] as $name=>$value)
		{
			$n = 'set'.ucfirst($name);
			$this->sql->$n($value);
		}

		return $this->sql;
	}

	/**
	 * Handle Database Request
	 *
	 * @param FormInterface $form
	 * @param Request       $request
	 *
	 * @return
	 */
	public function handleDataBaseRequest(FormInterface $form, Request $request)
	{

		$form->handleRequest($request);
		$this->saveDatabase = false;

		if (! $form->isSubmitted())
			return ;

		$data = $request->get('start_install');
		foreach ($this->sql->getPropertyNames() as $name)
		{
			$n = 'set'.ucfirst($name);
			switch ($name)
			{
				case 'path':
				case 'scheme':
				case 'server':
					break;
				default:
					$this->sql->$n($data[$name]);
			}
		}

		if ($form->isValid())
			$this->saveDatabase = $this->saveSQLParameters($this->sql);

		return ;
	}

	/**
	 * @return Database|null
	 */
	public function getSql(): ?Database
	{
		return $this->sql;
	}

	/**
	 * Parameter Status
	 *
	 * @return bool
	 */
	public function parameterStatus()
	{
		return is_writable($this->projectDir . '/.env');
	}

	/**
	 * Save SQL Parameters
	 *
	 * @param $params array
	 *
	 * @return bool
	 */
	public function saveSQLParameters()
	{
		$params = Yaml::parse(file_get_contents($this->projectDir.'/config/packages/doctrine.yaml'));
dump($params);
		$params['parameters']['db_driver'] = $this->sql->getDriver();
		$params['parameters']['db_host'] = $this->sql->getHost();
		$params['parameters']['db_port'] = $this->sql->getPort();
		$params['parameters']['db_name'] = $this->sql->getName();
		$params['parameters']['db_user'] = $this->sql->getUser();
		$params['parameters']['db_pass'] = $this->sql->getPass();
		$params['parameters']['db_prefix'] = $this->sql->getPrefix();
		$params['parameters']['db_server'] = $this->sql->getServer();

		if (file_put_contents($this->projectDir.'/config/packages/doctrine.yaml', Yaml::dump($params)))

		{
			$env = file($this->projectDir.'/.env');
			foreach($env as $q=>$w)
			{
				if (strpos($w, 'DATABASE_URL=') === 0)
					$env[$q] = $this->sql->getUrl();
				$env[$q] = trim($env[$q]);
			}
			$env = implode($env, "\r\n");
			return file_put_contents($this->projectDir.'/.env', $env);
		}

		return false;
	}

	/**
	 * Test Connected
	 *
	 * @param $params['parameters']
	 *
	 * @return mixed
	 */
	public function testConnected($sql, $factory)
	{
		unset($sql['name']);
		$this->sql->error = 'No Error Detected.';
		$this->connection = $factory->getConnection();

		try
		{
			$this->connection->connect();
		}
		catch (ConnectionException | \Exception $e)
		{
			$this->sql->error     = $e->getMessage();
			$this->sql->connected = false;
			$this->exception      = $e;
		}
		$this->sql->connected = $this->connection->isConnected();

		return $this->sql->connected;
	}

	/**
	 * @return mixed
	 */
	public function hasDatabase()
	{
		try
		{
			$this->connection->executeQuery("CREATE DATABASE IF NOT EXISTS " . $this->sql->getName() . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		}
		catch (SyntaxErrorException $e)
		{
			$this->sql->error     = $e->getMessage() . '. <strong>The database name must not have any spaces.</strong>';
			$this->sql->connected = false;
			$this->exception      = $e;

		}

		if ($this->sql->connected)
			$this->connection->executeQuery("ALTER DATABASE `" . $this->sql->getName() . "` CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`");

		return $this->sql->connected;
	}
}