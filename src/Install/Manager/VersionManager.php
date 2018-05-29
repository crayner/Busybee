<?php
namespace App\Install\Manager;

use App\Core\Manager\SettingManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Version;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\TranslatorInterface;

class VersionManager
{
	/**
	 * Version
	 */
	const VERSION = '0.0.34';

	/**
	 * @var SettingManager
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
	 * @var EntityManagerInterface
	 */
	private $entityManager;

    /**
     * @var int
     */
	private $SQLCount = 0;

    /**
     * @var bool
     */
	private $ready = false;

	/**
	 * VersionManager constructor.
	 *
	 * @param Connection              $connection
	 * @param SettingManagerInterface $settingManager
	 * @param                     $version
	 * @param TranslatorInterface     $translator
	 */
	public function __construct(EntityManagerInterface $entityManager, SettingManager $settingManager, TranslatorInterface $translator, RequestStack $request)
	{
		$this->connection     = $entityManager->getConnection();
		$this->settingManager = $settingManager;
        $this->version        = $this->settingManager->get('version', '0.0.00');
		$this->translator     = $translator;
		$this->entityManager  = $entityManager;

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
		$server = explode('-', $this->connection->getWrappedConnection()->getServerVersion());
		$versions['Database']['Server']                   = $server[1];
		$versions['Database']['Driver']                   = $this->connection->getParams()['driver'];
		$versions['Database']['Character Set']            = $this->connection->getParams()['charset'];
		$versions['Doctrine']['DBal']                     = \Doctrine\DBAL\Version::VERSION;

        foreach (get_loaded_extensions() as $name)
			$versions['PHP'][$name] = phpversion($name);


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
		$phpVersions['Core']['low']    = '7.2';
		$phpVersions['Core']['high']   = '7.2.99';
		$phpVersions['Core']['string'] = '7.2+';
        $phpVersions['apcu']['low']    = '5.1.9';
        $phpVersions['apcu']['high']   = '5.1.99';
        $phpVersions['apcu']['string'] = '5.1+';
        $phpVersions['intl']['string']           = '1.1+';
        $phpVersions['intl']['low']           = '1.1';
        $phpVersions['intl']['high']          = '1.1.99';
        $phpVersions['mbstring']['low']    = '7.2';
        $phpVersions['mbstring']['high']   = '7.2.99';
        $phpVersions['mbstring']['string'] = '7.2+';
        $phpVersions['zip']['low']    = '1.15';
        $phpVersions['zip']['high']   = '1.15.99';
        $phpVersions['zip']['string'] = '1.15+';
        $phpVersions['xml']['low']    = '7.2';
        $phpVersions['xml']['high']   = '7.2.99';
        $phpVersions['xml']['string'] = '7.2+';
        $phpVersions['Zend OPcache']['low']    = '7.2';
        $phpVersions['Zend OPcache']['high']   = '7.2.99';
        $phpVersions['Zend OPcache']['string'] = '7.2+';
        $phpVersions['gd']['low']    = '7.2';
        $phpVersions['gd']['high']   = '7.2.99';
        $phpVersions['gd']['string'] = '7.2+';
        $phpVersions['pdo_mysql']['low']    = '7.2';
        $phpVersions['pdo_mysql']['high']   = '7.2.99';
        $phpVersions['pdo_mysql']['string'] = '7.2+';
        $phpVersions['curl']['low']    = '7.2';
        $phpVersions['curl']['high']   = '7.2.99';
        $phpVersions['curl']['string'] = '7.2+';
        $phpVersions['json']['low']    = '1.6';
        $phpVersions['json']['high']   = '1.6.99';
        $phpVersions['json']['string'] = '1.6+';
		$phpVersions['openssl']['low']    = '7.2';
		$phpVersions['openssl']['high']   = '7.2.99';
		$phpVersions['openssl']['string'] = '7.2+';

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

		$version['low']                 = '10.2';
		$version['high']                = '10.2.99';
		$version['string']              = '10.2+ - Maria';
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
		$version['string']   = '4.1+';
		$version['low']      = '4.1';
		$version['high']     = '4.1.99';
		$versions['Symfony'] = $this->fullCompare($versions['Symfony'], $version);

		$version['string'] = '2.4+';
		$version['low']    = '2.4.0';
		$version['high']   = '2.4.99';
		$versions['Twig']  = $this->fullCompare($versions['Twig'], $version);

		$version['string']           = '2.8+';
		$version['low']                        = '2.8.0';
		$version['high']                       = '2.8.99';
		$versions['Doctrine']['Common']        = $this->fullCompare($versions['Doctrine']['Common'], $version);

		$version['string']             = '1.7+';
		$version['low']                = '1.7.0';
		$version['high']               = '1.7.99';
		$versions['Doctrine']['Cache'] = $this->fullCompare($versions['Doctrine']['Cache'], $version);

		$version['string']            = '2.7+';
		$version['low']               = '2.7';
		$version['high']              = '2.7.99';
		$versions['Doctrine']['DBal'] = $this->fullCompare($versions['Doctrine']['DBal'], $version);

        $version['string'] = '2.6+';
        $version['low']    = '2.6';
        $version['high']   = '2.6.99';
        $versions['Doctrine']['ORM'] = $this->fullCompare($versions['Doctrine']['ORM'], $version);


        foreach ($versions as $q => $w)
			if (is_array($w))
				ksort($versions[$q], SORT_STRING + SORT_FLAG_CASE);
		ksort($versions, SORT_STRING + SORT_FLAG_CASE);

		$versions['Settings'] = [];

		$versions['Settings']['Allow URL File Open']['value'] = ini_get('allow_url_fopen') == 1 ? 'On' : 'Off';
		$versions['Settings']['Memory Limit']['value'] = $this->translator->trans('memory.limit', ['%{limit}' => ini_get('memory_limit')], 'Install');
		$versions['Settings']['Session Auto Start']['value'] = ini_get('session.auto_start') == 1 ? 'session.auto_start.on' : 'session.auto_start.off';

		$versions['Settings']['Session Auto Start']['value'] = $this->translator->trans($versions['Settings']['Session Auto Start']['value'], [], 'Install');

		if (ini_get('allow_url_fopen'))
			$versions['Settings']['Allow URL File Open']['flag'] = 'alert alert-success';
		else
			$versions['Settings']['Allow URL File Open']['flag'] = 'alert alert-danger';
		if (ini_get('memory_limit') >= '512M')
			$versions['Settings']['Memory Limit']['flag'] = 'alert alert-success';
		elseif (ini_get('memory_limit') >= '256M')
			$versions['Settings']['Memory Limit']['flag'] = 'alert alert-warning';
		else
			$versions['Settings']['Memory Limit']['flag'] = 'alert alert-danger';
		if (ini_get('session.auto_start'))
			$versions['Settings']['Session Auto Start']['flag'] = 'alert alert-warning';
		else
			$versions['Settings']['Session Auto Start']['flag'] = 'alert alert-success';

        $this->setReady(true);
		foreach($versions as $w) {
		    if (isset($w['flag'])) {
                if (mb_strpos($w['flag'], 'danger') !== false) {
                    $this->setReady(false);
                    break;
                }
            } else {
                foreach ($w as $q) {
                    if (mb_strpos($q['flag'], 'danger') !== false) {
                        $this->setReady(false);
                        break;
                    }
                }
            }
            if (! $this->isReady())
                break;
		}

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
			$test['flag']  = 'alert alert-danger';
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

    /**
     * @return string
     */
    public function getInstalledVersion(): string
    {
        return $this->version;
    }

	/**
	 * @param $version
	 *
	 * @return string
	 */
	static function incrementVersion($version)
	{
		$parts = explode('.', $version);
		if (count($parts) !== 3)
		{
			trigger_error('This process only accepts standard 3 part versions. (0.0.00)');
			return $version;
		}

		if ($parts[2] + 1 < 99) {
			$parts[2]++;
		} else {
			$parts[2] = 0;
			if ($parts[1] + 1 < 99) {
				$parts[1]++;
			} else {
				$parts[1] = 0;
				$parts[0]++;
			}
		}

		$parts[2] = str_pad($parts[2], 2, '0', STR_PAD_LEFT);

		return implode('.', $parts);
	}

	/**
	 * @return bool
	 */
	public function isUpToDate()
	{
		$schemaTool = new SchemaTool($this->entityManager);

		$metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $this->SQLCount = $schemaTool->getUpdateSchemaSql($metaData);

		if (count($this->SQLCount) > 0)
			return false;

		return version_compare(self::VERSION, $this->version, '<=');
	}

	/**
	 * @return SettingManager
	 */
	public function getSettingManager(): SettingManager
	{
		return $this->settingManager;
	}

    /**
     * @return int
     */
    public function getSQLCount(): int
    {
        return $this->SQLCount;
    }

    /**
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->ready;
    }

    /**
     * @param bool $ready
     * @return VersionManager
     */
    public function setReady(bool $ready): VersionManager
    {
        $this->ready = $ready;
        return $this;
    }
}