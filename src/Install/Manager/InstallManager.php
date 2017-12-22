<?php
namespace App\Install\Manager;

use App\Install\Organism\Database;
use App\Install\Organism\Mailer;
use App\Install\Organism\Miscellaneous;
use Doctrine\DBAL\Exception\ConnectionException;
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
	 * @var bool
	 */
	private $mailerSaved = false;

	/**
	 * @var Mailer
	 */
	private $mailer;

	/**
	 * @var Miscellaneous
	 */
	private $misc;

	/**
	 * @var bool
	 */
	private $proceed = false;

	/**
	 * @var
	 */
	private $connection;

	/**
	 * InstallManager constructor.
	 *
	 * @param $projectDir   String
	 */
	public function __construct($projectDir)
	{
		$this->sql              = new Database();
		$this->projectDir       = $projectDir;
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
	public function testConnected()
	{
		$this->connection = $this->getConnection(false);

		$this->sql->error = 'No Error Detected.';
		$this->sql->setConnected(true);

		try
		{
			$this->connection->connect();
		}
		catch (ConnectionException $e)
		{
			$this->sql->error = $e->getMessage();
			$this->sql->setConnected(false);
			$this->exception = $e;
		}

		return $this->sql->isConnected();
	}

	/**
	 * @return mixed
	 */
	public function hasDatabase()
	{
		if ($this->sql->isConnected())
		{
			try
			{
				$this->connection->executeQuery("CREATE DATABASE IF NOT EXISTS " . $this->sql->getName() . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
			}
			catch (SyntaxErrorException $e)
			{
				$this->sql->error = $e->getMessage() . '. <strong>The database name is not valid.</strong>';
				$this->sql->setConnected(false);
				$this->exception = $e;

			}

			if ($this->sql->isConnected())
				$this->connection->executeQuery("ALTER DATABASE `" . $this->sql->getName() . "` CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`");
		}
		return $this->sql->isConnected();
	}

	/**
	 * Get Mailer Config
	 *
	 * @return array
	 */
	public function getMailerConfig()
	{
		$params =  Yaml::parse(file_get_contents($this->projectDir . '/config/packages/swiftmailer.yaml'));

		$this->mailer = new Mailer($params);

		return $this->mailer;
	}

	/**
	 * Save Mailer Config
	 *
	 * @param      $mailer
	 * @param bool $writeUrl
	 *
	 * @return bool
	 */
	public function saveMailerConfig($mailer, $writeUrl = true)
	{
		$this->mailer = $mailer;

		$this->mailerSaved = file_put_contents($this->projectDir . '/config/packages/swiftmailer.yaml', Yaml::dump($this->mailer->dumpMailerSettings()));

		if ($this->mailerSaved && $writeUrl)
		{
			$env = file($this->projectDir.'/.env');
			foreach($env as $q=>$w)
			{
				if (strpos($w, 'MAILER_URL=') === 0)
					$env[$q] = $this->mailer->getUrl();
				$env[$q] = trim($env[$q]);
			}
			$env = implode($env, "\r\n");
dump($env);
			$this->mailerSaved = file_put_contents($this->projectDir.'/.env', $env);
		}

		return $this->mailerSaved;
	}

	/**
	 * @return Mailer|null
	 */
	public function getMailer(): ?Mailer
	{
		return $this->mailer;
	}

	/**
	 * @param FormInterface $form
	 * @param Request       $request
	 *
	 * @return null
	 */
	public function handleMailerRequest(FormInterface $form, Request $request)
	{
		$form->handleRequest($request);
		$this->mailerSaved = false;

		if (!$form->isSubmitted())
			return;

		if ($form->isValid())
		{
			foreach ($request->get('install_mailer') as  $name => $value)
			{
				$name = explode('_', $name);
				foreach($name as $q=>$w)
					$name[$q] = ucfirst($w);
				$name = implode('', $name);
				$set = 'set' . ucfirst($name);

				$this->mailer->$set($value);
			}

			if ($this->mailer->getHost() === 'empty')
				$this->mailer->setHost(null);

			$this->saveMailerConfig($this->mailer);
		}

		return;
	}

	/**
	 * @return bool
	 */
	public function isMailerSaved(): bool
	{
		return $this->mailerSaved;
	}

	/**
	 * @return bool
	 */
	public function isProceed(): bool
	{
		return $this->proceed;
	}

	/**
	 * @param bool $proceed
	 *
	 * @return InstallManager
	 */
	public function setProceed(bool $proceed): InstallManager
	{
		$this->proceed = $proceed;

		return $this;
    }

    public function getMiscellaneousConfig()
    {
    	$this->misc = new Miscellaneous();

	    $params = Yaml::parse(file_get_contents($this->projectDir.'/config/packages/busybee.yaml'));

	    foreach($params['parameters'] as $name=>$value)
	    {
	    	$name = str_replace('_', ' ', $name);
	    	$name = explode(' ', $name);
	    	foreach($name as $q=>$w)
	    		$name[$q] = ucfirst($w);
	    	$name = implode('', $name);
	    	$set = 'set' . $name;

	    	if (method_exists($this->misc, $set))
	    		$this->misc->$set($value);
	    }

    	return $this->misc;
    }

	/**
	 * @param FormInterface $form
	 * @param Request       $request
	 * @throws \Exception
	 */
	public function handleMiscellaneousRequest(FormInterface $form, Request $request)
	{
		$this->proceed = false;
		$form->handleRequest($request);

		if (!$form->isSubmitted()) return;

		if ($form->isValid())
		{
			$params = Yaml::parse(file_get_contents($this->projectDir.'/config/packages/busybee.yaml'));
			foreach($request->get('install_miscellaneous') as $name=>$value)
			{
				$set = 'set'.ucfirst($name);
				$this->misc->$set($value);
			}

			$params['parameters'] = $this->misc->dumpMiscellaneousSettings($params['parameters']);

			try
			{
				file_put_contents($this->projectDir . '/config/packages/busybee.yaml', Yaml::dump($params));
			} catch( \Exception $e) {
				throw $e;
			}

			$this->proceed = true;
		}

		return;
	}

	/**
	 * @return Miscellaneous
	 */
	public function getMisc(): Miscellaneous
	{
		return $this->misc;
	}

	public function generatePassword()
	{
		$source = 'abcdefghijklmnopqrstuvwxyz';
		if ($this->misc->isPasswordNumbers())
			$source .= '0123456789';
		if ($this->misc->isPasswordMixedCase())
			$source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if ($this->misc->isPasswordSpecials())
			$source .= '#?!@$%^+=&*-';

		do {
			$password = '';
			for($x = 0; $x < $this->misc->getPasswordMinLength(); $x++)
				$password .= substr($source, random_int(0, strlen($source) - 1), 1);
		} while (! $this->isPasswordValid($password, $this->misc));

		return $password;
	}

	/**
	 * Is Password Valid
	 *
	 * @param               $password
	 * @param Miscellaneous $misc
	 *
	 * @return bool
	 */
	public function isPasswordValid($password, Miscellaneous $misc)
	{
		if ($misc instanceof Miscellaneous)
		{
			$pattern = "/^(.*(?=.*[a-z])";
			if ($misc->isPasswordMixedCase())
				$pattern .= "(?=.*[A-Z])";

			if ($misc->isPasswordNumbers())
				$pattern .= "(?=.*[0-9])";

			if ($misc->isPasswordSpecials())
				$pattern .= "(?=.*?[#?!@$%^+=&*-])";
			$pattern .= ".*){" . $misc->getPasswordMinLength() . ",}$/";

			return (preg_match($pattern, $password) === 1);
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function isMiscSaved(): bool
	{
		return $this->miscSaved;
	}

	public function getConnection($useDatabase = true)
	{
		$config = new \Doctrine\DBAL\Configuration();

		$connectionParams = [
			'driver' => $this->sql->getDriver(),
			'host' => $this->sql->getHost(),
			'port' => $this->sql->getPort(),
			'user' => $this->sql->getUser(),
			'password' => $this->sql->getPass(),
			'charset' => $this->sql->getCharset()
		];
		if ($useDatabase)
			$connectionParams['dbname'] = $this->sql->getName();

		$this->connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

		return $this->connection;

	}
}