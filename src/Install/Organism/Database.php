<?php
namespace App\Install\Organism;

use Doctrine\DBAL\Exception\InvalidArgumentException;

class Database
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $port = '3306';

	/**
	 * @var string
	 */
	private $user;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var string
	 */
	private $pass;

	/**
	 * @var string
	 */
	private $host;

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 * @var string
	 */
	private $scheme = 'mysql';

	/**
	 * @var string
	 */
	private $driver = 'pdo_mysql';

	/**
	 * @var string
	 */
	private $server = 'mysql';

	/**
	 * @var boolean
	 */
	private $connected = false;

	/**
	 * @return null|string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @param string|null $name
	 * @param bool   $path
	 *
	 * @return Database
	 */
	public function setName(string $name = null, bool $path = true): Database
	{
		$this->name = $name;

		if ($path && ! empty($name))
			return $this->setPath('/'.$name, false);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPort(): string
	{
		return $this->port;
	}

	/**
	 * @param string $port
	 *
	 * @return Database
	 */
	public function setPort(string $port): Database
	{
		$this->port = $port;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getUser(): ?string
	{
		return $this->user;
	}

	/**
	 * @param string $user
	 *
	 * @return Database
	 */
	public function setUser(string $user): Database
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getPath(): ?string
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 *
	 * @return Database
	 */
	public function setPath(string $path, $name = true): Database
	{
		$this->path = strpos($path, '/') !== 0 ? '/'.$path : $path;
		if ($name)
			return $this->setName(trim($path, '/'), false);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getScheme(): string
	{
		return $this->scheme;
	}

	/**
	 * @param string $schema
	 *
	 * @return Database
	 */
	public function setScheme(string $scheme): Database
	{
		$this->scheme = $scheme;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPass(): ?string
	{
		return $this->pass;
	}

	/**
	 * @param string $pass
	 *
	 * @return Database
	 */
	public function setPass(string $pass): Database
	{
		$this->pass = $pass;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getHost(): ?string
	{
		return $this->host;
	}

	/**
	 * @param string $host
	 *
	 * @return Database
	 */
	public function setHost(string $host): Database
	{
		$this->host = $host;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getDisplayForm(): bool
	{
		return false;
	}

	/**
	 * @return string
	 */
	public function getDriver(): string
	{
		return $this->driver;
	}

	/**
	 * @param string $driver
	 *
	 * @return Database
	 */
	public function setDriver(string $driver): Database
	{
		$this->driver = $driver;

		switch ($driver)
		{
			case 'pdo_mysql':
				$this->setScheme('mysql');
				$this->setServer('mysql');
				break;
			default:
				throw new InvalidArgumentException('Driver must be [pdo_mysql].');
		}

		return $this;
}

	/**
	 * @return string
	 */
	public function getPrefix(): ?string
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 *
	 * @return Database
	 */
	public function setPrefix(string $prefix): Database
	{
		$this->prefix = $prefix;

		return $this;
}

	/**
	 * @return string
	 */
	public function getServer(): string
	{
		return $this->server;
	}

	/**
	 * @param string $server
	 *
	 * @return Database
	 */
	public function setServer(string $server): Database
	{
		$this->server = $server;

		return $this;
    }

	/**
	 * @return array
	 */
	public function getPropertyNames(): array
    {
	    return [
	    	'name',
		    'port',
		    'user',
		    'path',
		    'pass',
		    'host',
		    'prefix',
		    'scheme',
		    'driver',
		    'server',
	    ];
    }

	/**
	 * @return string
	 */
	public function getUrl(): string
    {
	    return 'DATABASE_URL='. $this->getScheme().'://'.$this->getUser().':'.$this->getPass().'@'.$this->getHost().':'.$this->getPort().$this->getPath();
    }

	/**
	 * @return bool
	 */
	public function isConnected(): bool
	{
		return $this->connected;
	}

	/**
	 * @param bool $connected
	 *
	 * @return Database
	 */
	public function setConnected(bool $connected): Database
	{
		$this->connected = $connected;

		return $this;
}
}