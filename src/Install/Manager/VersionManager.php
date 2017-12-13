<?php
namespace App\Install\Manager;

use App\Core\Manager\SettingManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Version;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\TranslatorInterface;

class VersionManager
{
	/**
	 * Version
	 */
	const VERSION = '0.0.04';

	/**
	 * @var SettingManagerInterface
	 */
	private $settingManager;

	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @var array
	 */
	private $version;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * VersionManager constructor.
	 *
	 * @param Connection              $connection
	 * @param SettingManagerInterface $settingManager
	 * @param                     $version
	 * @param TranslatorInterface     $translator
	 */
	public function __construct(Connection $connection, SettingManager $settingManager, TranslatorInterface $translator, RequestStack $request)
	{
		$this->connection     = $connection;
		$this->settingManager = $settingManager;
		$this->version        = $request->getCurrentRequest()->get('version');
		$this->translator     = $translator;

		return $this;
	}

	/**
	 * Get Version
	 *
	 * @return array
	 */
	public function getAcknowledgements($install = false, $connection = null)
	{
		if ($connection instanceof Connection)
			$this->connection = $connection;

		$versions = [];

		$versions['Twig']                                 = \Twig_Environment::VERSION;
		$versions['Symfony']                              = Kernel::VERSION;
		$versions['Doctrine']['ORM']                      = Version::VERSION;
		$versions['Doctrine']['Common']                   = \Doctrine\Common\Version::VERSION;
		$versions['Doctrine']['Cache']                    = \Doctrine\Common\Cache\Version::VERSION;
		$versions['Database']['Server']                   = str_replace('-MariaDB', '', $this->connection->getWrappedConnection()->getServerVersion());
		$versions['Database']['Driver']                   = $this->connection->getParams()['driver'];
		$versions['Database']['Character Set']            = $this->connection->getParams()['charset'];
		$versions['Doctrine']['DBal']                     = \Doctrine\DBAL\Version::VERSION;
		foreach (get_loaded_extensions() as $name)
			$versions['PHP'][$name] = phpversion($name);

		$versions['PHP']['memory'] = ini_get('memory_limit');

		foreach ($versions as $q => $w)
		{
			if (is_array($w))
			{
				foreach ($w as $e => $r)
				{
					unset($versions[$q][$e]);
					$versions[$q][$e]['value'] = $r;
					$versions[$q][$e]['flag']  = false;

				}
			}
			else
			{
				unset($versions[$q]);
				$versions[$q]['value'] = $w;
				$versions[$q]['flag']  = false;
			}
		}

		if (!$install)
		{
			$versions['Busybee']['Version'] = $this->settingManager->get('version');

			if ($versions['Busybee']['Version'] !== $this->version)
			{
				$versions['Busybee']['Version']          = [];
				$versions['Busybee']['Version']['flag']  = 'alert alert-warning';
				$versions['Busybee']['Version']['value'] = $this->translator->trans('software.required', ['%required%' => $this->version], 'Install');
			}
			else
			{
				$versions['Busybee']['Version']          = [];
				$versions['Busybee']['Version']['flag']  = 'alert alert-success';
				$versions['Busybee']['Version']['value'] = $this->translator->trans('version.equal', ['%required%' => $this->version], 'Install');
			}
		}

		$phpVersions                   = [];
		$phpVersions['Core']['low']    = '7.1.0';
		$phpVersions['Core']['high']   = '7.2.99';
		$phpVersions['Core']['string'] = '7.1';
		$phpVersions['apcu']           = '5.1.8';
		$phpVersions['memory']['string']          = '256M - 1024M';
		$phpVersions['memory']['low']          = '256M';
		$phpVersions['memory']['high']          = '1024M';
		$phpVersions['intl']           = '1.1.0';
		$phpVersions['json']['low']    = '1.5.0';
		$phpVersions['json']['high']   = '1.6.99';
		$phpVersions['json']['string'] = '1.6.x';
		$phpVersions['openssl']['low']    = '7.1.0';
		$phpVersions['openssl']['high']   = '7.2.99';
		$phpVersions['openssl']['string'] = '7.x';

		foreach ($versions['PHP'] as $name => $w)
			if (!isset($phpVersions[$name]))
				unset($versions['PHP'][$name]);

		foreach ($phpVersions as $name => $version)
			if (!is_array($version))
			{
				$phpVersions[$name]           = [];
				$phpVersions[$name]['low']    = $version;
				$phpVersions[$name]['high']   = $version;
				$phpVersions[$name]['string'] = $version;
			}

		foreach ($phpVersions as $name => $version)
		{
			if (!isset($versions['PHP'][$name]))
			{
					$versions['PHP'][$name]['value'] = $this->translator->trans('software.required', ['%required%' => $version['string']], 'Install');
					$versions['PHP'][$name]['flag']  = 'alert alert-danger';
			}
			elseif (! in_array($name, ['memory']))
				$versions['PHP'][$name] = $this->fullCompare($versions['PHP'][$name], $version);
		}
		dump($versions['PHP']);dump($phpVersions);
		if ($versions['PHP']['memory']['value'] < $phpVersions['memory']['low'])
		{
			$versions['PHP']['memory']['value'] = $this->translator->trans('php.memory.small', ['%{required}' => $phpVersions['memory']['string'], '%{memory}' => $versions['PHP']['memory']['value']], 'Install');
			$versions['PHP']['memory']['flag']  = 'alert alert-warning';
		} else {
			$versions['PHP']['memory']['value'] = $this->translator->trans('php.memory.ok', ['%{required}' => $phpVersions['memory']['string'], '%{memory}' => $versions['PHP']['memory']['value']], 'Install');
			$versions['PHP']['memory']['flag']  = 'alert alert-success';
		}



		$version['low']                 = '5.5.56';
		$version['high']                = '5.7';
		$version['string']              = '5.5 - 5.7';
		$versions['Database']['Server'] = $this->fullCompare($versions['Database']['Server'], $version);

		$version = 'utf8mb4';
		if ($versions['Database']['Character Set']['value'] !== $version)
		{
			$versions['Database']['Character Set']['flag']  = 'alert alert-danger';
			$versions['Database']['Character Set']['value'] .= $this->translator->trans('setting.required', ['%required%' => $version], 'Install');
		}
		else
			$versions['Database']['Character Set']['flag'] = 'alert alert-success';

		$version = 'pdo_mysql';
		if ($versions['Database']['Driver']['value'] !== $version)
		{
			$versions['Database']['Driver']['flag']  = 'alert alert-danger';
			$versions['Database']['Driver']['value'] .= $this->translator->trans('setting.required', ['%required%' => $version], 'Install');
		}
		else
			$versions['Database']['Driver']['flag'] = 'alert alert-success';

		$version             = [];
		$version['string']   = '4.x';
		$version['low']      = '4.0.0';
		$version['high']     = '4.9.99';
		$versions['Symfony'] = $this->fullCompare($versions['Symfony'], $version);

		$version['string'] = '2.4.0 - 2.4.9';
		$version['low']    = '2.4.0';
		$version['high']   = '2.4.9';
		$versions['Twig']  = $this->fullCompare($versions['Twig'], $version);

		$version['string']           = '2.8+';
		$version['low']                        = '2.8.0';
		$version['high']                       = '2.8.99';
		$versions['Doctrine']['Common']        = $this->fullCompare($versions['Doctrine']['Common'], $version);

		$version['string']             = '1.7+';
		$version['low']                = '1.7.0';
		$version['high']               = '1.7.99';
		$versions['Doctrine']['Cache'] = $this->fullCompare($versions['Doctrine']['Cache'], $version);

		$version['string']            = '2.6+';
		$version['low']               = '2.6.0';
		$version['high']              = '2.6.99';
		$versions['Doctrine']['DBal'] = $this->fullCompare($versions['Doctrine']['DBal'], $version);

		$version['string'] = '2.5+';
		$version['low']    = '2.5.6';
		$version['high']   = '2.5.99';
		$versions['Doctrine']['ORM'] = $this->fullCompare($versions['Doctrine']['ORM'], $version);


		foreach ($versions as $q => $w)
			if (is_array($w))
				ksort($versions[$q], SORT_STRING + SORT_FLAG_CASE);
		ksort($versions, SORT_STRING + SORT_FLAG_CASE);


		$versions['Settings'] = [];

		$versions['Settings']['Allow URL File Open']['value'] = ini_get('allow_url_fopen') == 1 ? 'On' : 'Off';
		if (ini_get('allow_url_fopen'))
			$versions['Settings']['Allow URL File Open']['flag'] = 'alert alert-success';
		else
			$versions['Settings']['Allow URL File Open']['flag'] = 'alert alert-danger';

		return $versions;
	}

	/**
	 * Full Compare
	 *
	 * @param iterable $test
	 * @param iterable $standard
	 *
	 * @return iterable
	 */
	private function fullCompare(iterable $test, iterable $standard): iterable
	{
		if (version_compare($test['value'], $standard['low'], '<'))
		{
			$test['flag']  = 'alert alert-warning';
			$test['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $standard['string']], 'Install');
		}
		elseif (version_compare($test['value'], $standard['low'], '>=') && version_compare($test['value'], $standard['high'], '<='))
		{
			$test['flag']  = 'alert alert-success';
			$test['value'] .= $this->translator->trans('version.equal', ['%required%' => $standard['string']], 'Install');
		}
		elseif (version_compare($test['value'], $standard['high'], '>'))
		{
			$test['flag']  = 'alert alert-info';
			$test['value'] .= $this->translator->trans('version.over', ['%required%' => $standard['string']], 'Install');
		}

		return $test;
	}

	/**
	 * @return string
	 */
	public function getVersion(): string
	{
		return self::VERSION;
	}
}