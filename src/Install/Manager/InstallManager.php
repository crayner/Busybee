<?php
namespace App\Install\Manager;

use App\Core\Manager\MailerManager;
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
    protected $projectDir;

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
     * @var MailerManager
     */
    private $mailerManager;

    /**
     * InstallManager constructor.
     *
     * @param $projectDir   String
     */
    public function __construct($projectDir)
    {
        $this->sql = new Database();
        $this->projectDir = $projectDir;
        $this->mailerManager = new MailerManager($projectDir);
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
            if (strpos($name, 'db_') === 0)
                $params['parameters'][str_replace('db_', '', $name)] = $value;

        $params['parameters'] = array_merge($params['parameters']);

        foreach ($params['parameters'] as $name => $value) {
            $n = 'set' . ucfirst($name);
            $this->sql->$n($value);
        }

        return $this->sql;
    }

    /**
     * Handle Database Request
     *
     * @param FormInterface $form
     * @param Request $request
     *
     * @return
     */
    public function handleDataBaseRequest(FormInterface $form, Request $request)
    {

        $form->handleRequest($request);
        $this->saveDatabase = false;

        if (!$form->isSubmitted())
            return;

        $data = $request->get('start_install');
        foreach ($this->sql->getPropertyNames() as $name) {
            $n = 'set' . ucfirst($name);
            switch ($name) {
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

        return;
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
        $params = Yaml::parse(file_get_contents($this->projectDir . '/config/packages/doctrine.yaml'));

        $params['parameters']['db_driver'] = $this->sql->getDriver();
        $params['parameters']['db_host'] = $this->sql->getHost();
        $params['parameters']['db_port'] = $this->sql->getPort();
        $params['parameters']['db_name'] = $this->sql->getName();
        $params['parameters']['db_user'] = $this->sql->getUser();
        $params['parameters']['db_pass'] = $this->sql->getPass();
        $params['parameters']['db_prefix'] = $this->sql->getPrefix();
        $params['parameters']['db_server'] = $this->sql->getServer();

        if (file_put_contents($this->projectDir . '/config/packages/doctrine.yaml', Yaml::dump($params))) {
            $env = file($this->projectDir . '/.env');
            foreach ($env as $q => $w) {
                if (strpos($w, 'DATABASE_URL=') === 0)
                    $env[$q] = $this->sql->getUrl();
                $env[$q] = trim($env[$q]);
            }
            $env = implode($env, "\r\n");
            return file_put_contents($this->projectDir . '/.env', $env);
        }

        return false;
    }

    /**
     * Test Connected
     *
     * @param $params ['parameters']
     *
     * @return mixed
     */
    public function testConnected()
    {
        $this->connection = $this->getConnection(false);

        $this->sql->error = 'No Error Detected.';
        $this->sql->setConnected(true);

        try {
            $this->connection->connect();
        } catch (ConnectionException $e) {
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
        if ($this->sql->isConnected()) {
            try {
                $this->connection->executeQuery("CREATE DATABASE IF NOT EXISTS " . $this->sql->getName() . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (SyntaxErrorException $e) {
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

        $params = Yaml::parse(file_get_contents($this->projectDir . '/config/packages/busybee.yaml'));

        foreach ($params['parameters'] as $name => $value) {
            $name = str_replace('_', ' ', $name);
            $name = explode(' ', $name);
            foreach ($name as $q => $w)
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
     * @param Request $request
     * @throws \Exception
     */
    public function handleMiscellaneousRequest(FormInterface $form, Request $request)
    {
        $this->proceed = false;
        $form->handleRequest($request);

        if (!$form->isSubmitted()) return;

        if ($form->isValid()) {
            $params = Yaml::parse(file_get_contents($this->projectDir . '/config/packages/busybee.yaml'));
            foreach ($request->get('install_miscellaneous') as $name => $value) {
                if ($name !== '_token') {
                    $set = 'set' . ucfirst($name);
                    $this->misc->$set($value);
                }
            }

            $params['parameters'] = $this->misc->dumpMiscellaneousSettings($params['parameters']);

            try {
                file_put_contents($this->projectDir . '/config/packages/busybee.yaml', Yaml::dump($params));
            } catch (\Exception $e) {
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

    /**
     * @return bool
     */
    public function isMiscSaved(): bool
    {
        return $this->miscSaved;
    }

    /**
     * @param bool $useDatabase
     *
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
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

    /**
     * @return MailerManager
     */
    public function getMailerManager(): MailerManager
    {
        return $this->mailerManager;
    }
}