<?php
namespace App\Core\Manager;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\DBAL\Driver\PDOException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Setting Manager
 *
 * @version    6th September 2017
 * @since      20th October 2016
 * @author     Craig Rayner
 */
class SettingManager implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var SettingRepository
     */
    private $settingRepository;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorisation;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var bool
     */
    private $installMode = false;

    /**
     * SettingManager constructor.
     *
     * @param ContainerInterface $container
     * @param SessionInterface $session
     * @param SettingRepository $settingRepository
     */
    public function __construct(ContainerInterface $container, SessionInterface $session,
                                SettingRepository $settingRepository, MessageManager $messageManager,
                                AuthorizationCheckerInterface $authorisation, \Twig_Environment $twig)
    {
        $this->setContainer($container);
        $this->session = $session;
        $this->settingRepository = $settingRepository;
        $this->messageManager = $messageManager;
        $this->authorisation = $authorisation;
        $this->twig = $twig;
    }

    /**
     * Set Container
     *
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @var bool
     */
    private $flip = false;

    /**
     * get Setting
     *
     * @version    31st October 2016
     * @since      20th October 2016
     *
     * @param    string $name
     * @param    mixed  $default
     * @param    array  $options
     *
     * @return    mixed    Value
     */
    public function get($name, $default = null, $options = [])
    {
        $this->readSession();

        $name = strtolower($name);
        $this->flip                = false;
        if (substr($name, -6) === '._flip')
        {
            $this->flip = true;
            $name = str_replace('._flip', '', $name);
        }

        $this->setName($name);

        $this->settingExists[$name] = isset($this->settingExists[$name]) ? (bool)$this->settingExists[$name] : false ;

        $this->loadSetting($name);

        if ($this->settingExists[$name])
        {
            if (empty($this->setting))
                $this->setting = $this->settings[$name];

            $func = 'get' . ucfirst($this->setting->getType());
            $value = $this->$func($name, $default, $options);
            if ($this->flip && is_array($value))
                return array_flip($value);
            else
                return $value;
        }

        if (mb_strpos($name, '.') !== false)
        {
            $n = explode('.', $name);
            array_pop($n);
            $name = implode('.', $n);
            $value = $this->get($name, $default, $options);
            if ($this->flip && is_array($value))
                return array_flip($value);
            else
                return $value;
        }

        return $default;
    }

    /**
     * @var array
     */
    private $settings;

    /**
     * @var array
     */
    private $settingCache;

    /**
     * @var array
     */
    private $settingExists;

    /**
     * Read Session
     */
    private function readSession()
    {
        if ($this->session->isStarted()) {
            $this->settings = $this->session->get('settings');
            $this->settingCache = $this->session->get('settingCache');
            $this->settingExists = $this->session->get('settingExists');
        }
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return SettingManager
     */
    public function setName(string $name): SettingManager
    {
        if (mb_strpos($this->name, $name) !== 0 || empty($this->name))
            $this->name = $name;

        return $this;
    }

    /**
     * @var Setting
     */
    private $setting;

    /**
     * @param string $name
     * @param bool $reload
     * @return null|Setting
     */
    private function loadSetting(string $name, $reload = false): ?Setting
    {
        if ($this->settingExists[$name])
        {
            if (empty($this->settings[$name]) || empty($this->settingCache[$name]) || $this->settingCache[$name]->getTimestamp() > strtotime('-15 Minutes'))
                $reload = true;
        } else
            $reload = true;

        if ($reload) {
            $this->setting = $this->findOneByName($name);

            if ($this->setting instanceof Setting) {
                $this->flushToSession($this->setting);
            }
        } else
            $this->setting = $this->settings[$name];

        return $this->setting;
    }

    /**
     * Write Session
     */
    private function writeSession()
    {
        if ($this->session->isStarted()){
            $this->session->set('settings', $this->settings);
            $this->session->set('settingCache', $this->settingCache);
            $this->session->set('settingExists', $this->settingExists);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        $this->get($name);
        $this->settingExists[$name] = isset($this->settingExists[$name]) ?  $this->settingExists[$name] : false ;
        return (bool) $this->settingExists[$name];
    }

    /**
     * Get parameter
     *
     * @param   string $name
     * @param   mixed  $default
     *
     * @return  mixed
     */
    public function getParameter($name, $default = null)
    {
        if ($this->hasParameter($name))
            return $this->container->getParameter($name);

        if (false === strpos($name, '.'))
            return $default;

        $pName = explode('.', $name);

        $key = array_pop($pName);

        $name = implode('.', $pName);

        $value = $this->getParameter($name, $default);

        if (is_array($value) && isset($value[$key]))
            return $value[$key];

        throw new \InvalidArgumentException(sprintf('The value %s is not a valid array parameter.', $name));
    }

    /**
     * Has parameter
     *
     * @param   string $name
     * @param   mixed  $default
     *
     * @return  mixed
     */
    public function hasParameter($name)
    {
        return $this->container->hasParameter($name);
    }

    /**
     * @return bool
     */
    public function isInstallMode(): bool
    {
        return $this->installMode;
    }

    /**
     * @param bool $installMode
     * @return SettingManager
     */
    public function setInstallMode(bool $installMode): SettingManager
    {
        $this->installMode = $installMode;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getSystem(string $name, $default, $options)
    {
        $value = $this->setting->getValue();

        return empty($value) ? $this->getDefault($default) : $value ;
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getArray(string $name, $default, $options)
    {
        $value = $this->setting->getValue();
        if ($this->name === $name)
            return $value;

        $extension = trim(str_replace($name, '', $this->name), '.');

        $extension = explode('.', $extension);

        $x = [];
        if (is_array($value)) {
            foreach ($value as $q => $w)
                $x[strtolower($q)] = $w;
            $value = $x;
        }

        foreach($extension as $ext)
        {
            if (key_exists($ext, $value))
            {
                $this->setting = new Setting();
                $name .= '.' . $ext;
                $this->setting->setName(strtolower($name));
                if (is_array($value[$ext]))
                    $this->setting->setType('array');
                else
                    $this->setting->setType('system');
                $value = $value[$ext];
                $this->setting->setValue($value);
                $this->flushToSession($this->setting);
                if ($this->name === $name)
                    if ($this->flip && is_array($value))
                        return array_flip($value);
                    else
                        return $value;
            }
            else
            {
                $value = null;
            }
        }

        return empty($value) ? $this->getDefault($default) : $value;
    }

    /**
     * @param $default
     * @return mixed
     */
    private function getDefault($default)
    {
        if (empty($default))
            $default = $this->setting->getDefaultValue();
        return $default;

    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getImage(string $name, $default, $options): ?string
    {
        $value = $this->setting->getValue();

        return empty($value) ? $this->getDefault($default) : $value ;
    }

    /**
     * @param Request       $request
     * @param FormInterface $form
     */
    public function handleImportRequest(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $form->get('import_file')->getData();
            try
            {
                $data = Yaml::parse(file_get_contents($file->getPathName()));
            } catch (\Exception $e) {
                $this->messageManager->add('danger', 'setting.import.import.error', ['%{message}' => $e->getMessage()], 'Setting');
                return ;
            }

            if ($data['name'] !== $form->get('import_file')->getData()->getClientOriginalName())
            {
                $this->messageManager->add('danger', 'setting.import.name.error', ['%{name}' => $data['name']], 'Setting');
                return;
            }

            $this->buildSettings($this->convertSettings($data['settings']), $data['name']);
        }
    }

    /**
     * @param $data
     * @param $resource
     */
    private function buildSettings($data, $resource)
    {
        if (empty($data))
            return;
        foreach ($data as $name => $datum)
        {
            $entity = $this->findOneByName($name);

            if (!$entity instanceof Setting)
            {
                $entity = new Setting();
                $entity->setName($name);
                if (empty($datum['type']))
                {
                    $this->messageManager->addMessage('warning', 'setting.resource.warning', ['{{name}}' => $name], 'System');
                    continue;
                }
                $entity->setType($datum['type']);
            }
            foreach ($datum as $field => $value)
            {
                $w = 'set' . ucwords($field);
                $entity->$w($value);
            }
            $this->createSetting($entity);
        }

        $this->messageManager->addMessage('success', 'setting.resource.success', ['{{name}}' => $resource], 'System');
    }

    /**
     * @param $name
     * @return Setting|null
     */
    public function findOneByName($name): ?Setting
    {
        try {
            return $this->settingRepository->findOneByName($name);
        } catch (\Exception $e) {
            if ($e->getPrevious() instanceof PDOException && in_array($e->getErrorCode(),['1146', '1045']))
                return null;
            throw $e;
        }
    }

    /**
     * create Setting
     *
     * @version 5th April 2017
     * @since   21st October 2016
     *
     * @param   Setting
     *
     * @return  SettingManager
     */
    public function createSetting(Setting $setting)
    {
        if (!$this->has($setting->getName()))
        {
            $func = 'set'.ucfirst(strtolower($setting->getType()));
            $setting->setValue($this->$func($setting->getValue()));

            $em = $this->container->get('doctrine')->getManager();
            $em->persist($setting);
            $em->flush();
        }
        elseif (! empty($setting->getValue()))
        {
            $this->set($setting->getName(), $setting->getValue());
        }
        else
        {
            $this->get($setting->getName());
        }

        return $this;
    }

    /**
     * set Setting
     *
     * @version 31st October 2016
     * @since   21st October 2016
     *
     * @param   string $name
     * @param   mixed  $value
     *
     * @return  mixed
     */
    public function set($name, $value)
    {
        $name          = strtolower($name);

        $this->setting = $this->findOneByName($name);
        if (is_null($this->setting) || empty($this->setting->getName()))
            return $this;

        if (! $this->isInstallMode())
            if (! $this->authorisation->isGranted($this->setting->getRole(), $this->setting))
                return $this;

        $func = 'set' . ucfirst(strtolower($this->setting->getType()));
        $value = $this->$func($value);

        $this->setting->setValue($value);
        $em = $this->container->get('doctrine')->getManager();
        $em->persist($this->setting);
        $em->flush();
        $this->flushToSession($this->setting);

        return $this;
    }

    private function flushToSession(Setting $setting)
    {
        $name = strtolower($setting->getName());
        $this->settings[$name] = $setting;
        $this->settingCache[$name] = new \DateTime();
        $this->settingExists[$name] = true;
        $this->writeSession();
    }

    /**
     * @param int $id
     * @return Setting|null
     */
    public function find(int $id): ?Setting
    {
        $this->setting = $this->settingRepository->find($id);

        if ($this->setting instanceof Setting)
            $this->flushToSession($this->setting);

        return $this->setting;
    }

    public function getSetting(): ?Setting
    {
        return $this->setting;
    }

    private function setArray($value)
    {
        if (is_array($value))
            $value = Yaml::dump($value);

        return $value;
    }

    private function getBoolean(string $name, $default, $options): ?string
    {
        $value = $this->setting->getValue();

        return (bool) $value ;
    }

    private function getTwig(string $name, $default, $options): ?string
    {
        $value = $this->setting->getValue();

        return empty($value) ? $this->getDefault($default) : $value ;
    }

    private function setTwig($value)
    {
        if (is_null($value)) $value = '{{ empty }}';

        try
        {
            $x = $this->twig->createTemplate($value)->render([]);
        }
        catch (\Twig_Error_Syntax $e)
        {
            throw $e;
        }
        catch (\Twig_Error_Runtime $e)
        {
            // Ignore Runtime Errors
        }

        return $value;
    }

    private function getString(string $name, $default, $options): ?string
    {
        $value = $this->setting->getValue();

        return empty($value) ? $this->getDefault($default) : $value ;
    }

    private function setString($value)
    {
        return strval(mb_substr($value, 0, 25));
    }

    private function setImage($value): ?string
    {
        return $value;
    }

    private function setBoolean($value): bool
    {
        return (bool) $value;
    }

    public function getInteger(string $name, $default, $options): int
    {
        $value = $this->setting->getValue();

        return empty($value) ? intval($this->getDefault($default)) : intval($value) ;
    }

    private function setInteger($value): int
    {
        return intval($value);
    }

    private function getRegex(string $name, $default, $options): ?string
    {
        $value = $this->setting->getValue();

        return empty($value) ? $this->getDefault($default) : $value ;
    }

    private function setRegex(?string $value): ?string
    {
        return $value;
    }

    private function setSystem($value)
    {
        return $value;
    }

    private function getText(string $name, $default, $options): ?string
    {
        $value = $this->setting->getValue();

        return empty($value) ? $this->getDefault($default) : $value ;
    }

    private function setText(?string $value): ?string
    {
        return $value;
    }

    private function getTime(string $name, $default, $options)
    {
        $value = $this->setting->getValue();
        $value = empty($value) ? $this->getDefault($default) : $value ;

        return $value;
    }

    private function setTime($value): ?string
    {
        if (!empty($value) && $value instanceof \DateTime)
            $value = $value->format('H:i');

        return $value;
    }

    private function getEnum(string $name, $default, $options)
    {
        return $this->getString($name, $default, $options);
    }

    private function setEnum($value): ?string
    {
        return $this->setString($value);
    }
}