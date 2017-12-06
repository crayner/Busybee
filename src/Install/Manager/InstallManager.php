<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 5/12/2017
 * Time: 16:09
 */

namespace App\Install\Manager;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class InstallManager
{
	/**
	 * @var \stdClass
	 */
	private $sql;

	/**
	 * @var string
	 */
	private $projectDir;

	/**
	 * InstallManager constructor.
	 */
	public function __construct(string $projectDir)
	{
		$this->sql             = new \stdClass();
		$this->projectDir = $projectDir;
	}

	/**
	 * Get SQL Parameters
	 *
	 * @param array $params
	 *
	 * @return null|array
	 */
	public function getSQLParameters(array $params): ?array
	{
		$sql = [];
		foreach ($params as $name => $value)

			if ($name !== 'database_prefix' &&strpos($value, 'DATABASE_URL') === 0)
				$sql['DATABASE_URL'] = parse_url(trim(str_replace(['DATABASE_URL=', 'db_port'], ['', 3306], $value)));
		elseif ($name === 'database_prefix')
			$sql['prefix'] = $value;

		$db = $sql['DATABASE_URL'];
		unset($sql['DATABASE_URL']);
		$sql = array_merge($db, $sql);

		return $sql;
	}

	/**
	 * Get Parameters
	 *
	 * @return array
	 */
	public function getParameters()
	{
		$params = file($this->projectDir . '/.env');

		$x = Yaml::parse(file_get_contents($this->projectDir . '/config/packages/busybee.yaml'));
		$x = $x['parameters'];

		$params = array_merge($params, $x);

		return $params;
	}

	/**
	 * Handle Database Request
	 *
	 * @param FormInterface $form
	 * @param Request       $request
	 *
	 * @return array
	 */
	public function handleDataBaseRequest(FormInterface $form, Request $request)
	{

		$form->handleRequest($request);
		$this->saveDatabase = false;

		$sql = [];
		foreach ((array) $this->sql as $name => $value)
		{
			if (strpos($name, 'database_') === 0)
			{
				$sql[str_replace('database_', '', $name)] = $value;
			}
		}
		if (!$form->isSubmitted())
			return $sql;

		foreach ($sql as $name => $value)
		{
			$sql[$name]    = $form->get($name)->getData();
			$n             = 'database_' . $name;
			$this->sql->$n = $form->get($name)->getData();
		}

		if ($form->isValid())
		{
			$params = $this->getParameters();
			foreach ((array) $this->sql as $name => $value)
				if (strpos($name, 'database_') === 0)
					$params[$name] = $value;

			$this->saveDatabase = $this->saveParameters($params);

		}

		return $sql;
	}

	/**
	 * @return \stdClass|null
	 */
	public function getSql(): ?\stdClass
	{
		return $this->sql;
	}

}