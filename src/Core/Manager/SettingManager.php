<?php
namespace App\Core\Manager;

use App\Entity\Setting;
use App\Core\Organism\SettingCache;
use App\Repository\SettingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return SettingManager
     */
    public function setContainer(ContainerInterface $container = null): SettingManager
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorisation;

    /**
     * @var TwigManager
     */
    private $twig;

    /**
     * SettingManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, MessageManager $messageManager, AuthorizationCheckerInterface $authorisation, TwigManager $twig)
    {
        $this->setContainer($container);
        $this->messageManager = $messageManager;
        $this->authorisation = $authorisation;
        $this->twig = $twig;
    }

    /**
     * get Setting
     *
     * @version    31st October 2016
     * @since      20th October 2016
     *
     * @param    string $name
     * @param    mixed $default
     * @param    array $options
     *
     * @return    mixed    Value
     */
    public function get($name, $default = null, $options = [])
    {
        $name = strtolower($name);
        if ($this->readSession()->setName($name)->isSettingExists($name))
            return $this->getValue($name, $default, $options);

        $this->loadSetting($name, $default, $options);

        if ($this->getSetting($name))
            return $this->getValue($name, $default, $options);

        return $default;
    }

    /**
     * @var ArrayCollection
     */
    private $settings;

    /**
     * Read Session
     */
    private function readSession(): SettingManager
    {
        if ($this->hasSession())
            $this->settings = $this->getSession()->get('settings');
        else
            $this->settings = new ArrayCollection();
        return $this;
    }

    /**
     * hasSession
     *
     * @return bool
     */
    public function hasSession(): bool
    {
        return $this->getRequest()->hasSession();
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): ?SessionInterface
    {
        if ($this->hasSession())
            return $this->getRequest()->getSession();

        return null;
    }

    /**
     * @var Request
     */
    private $request;

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        if (empty($this->request))
            $this->request = $this->getContainer()->get('request_stack')->getCurrentRequest();

        return $this->request;
    }

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var bool
     */
    private $flip = false;

    /**
     * @param string $name
     * @return SettingManager
     */
    public function setName(string $name): SettingManager
    {
        $name = strtolower($name);
        $this->flip = false;
        if (substr($name, -6) === '._flip') {
            $this->flip = true;
            $name = str_replace('._flip', '', $name);
        }

        if (empty($this->name) || mb_strpos($this->name, $name) !== 0)
            $this->name = $name;

        return $this;
    }

    /**
     * @var SettingCache
     */
    private $setting;

    /**
     * getSetting
     *
     * @return SettingCache|null
     */
    public function getSetting(string $name): ?SettingCache
    {
        if ($this->setting && $this->setting->getName() === $name)
            return $this->setting;
        if ($this->getSettings()->containsKey($name))
            $this->setting = $this->settings->get($name);
        else
            $this->setting = null;

        return $this->setting;
    }

    /**
     * isSettingExists
     *
     * @param string $name
     * @return bool
     */
    private function isSettingExists(string $name): bool
    {
        if ($this->getSetting($name) === null)
            return false;
        if ($this->getSetting($name)->getName() === strtolower($name) && $this->setting->isValidSetting())
            return true;

        return false;
    }

    /**
     * getSettings
     *
     * @return ArrayCollection
     */
    private function getSettings(): ArrayCollection
    {
        if (empty($this->settings))
            $this->settings = new ArrayCollection();

        return $this->settings;
    }

    /**
     * has
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        $this->get(strtolower($name));
        return $this->isSettingExists(strtolower($name));
    }

    /**
     * @param string $name
     * @param bool $reload
     * @return null|Setting
     */
    private function loadSetting(string $name, $default = null, array $options = []): SettingManager
    {
        $setting = $this->findOneByName($name);

        if ($setting instanceof Setting)
            return $this->writeSettingCache($setting);

        $parts = explode('.', $name);

        if (count($parts) > 1) {
            $part = array_pop($parts);
            $name = implode('.', $parts);
            $value = $this->get($name, $default, $options);

            if ($this->setting && $this->setting instanceof SettingCache && $this->setting->getName() === $name) {
                if ($this->setting->getSetting()->getType() !== 'array')
                    return $this;
                foreach ($this->setting->getValue() as $name => $value) {
                    if (strtolower($part) === strtolower($name)) {
                        $setting = new Setting();
                        $setting->setType(is_array($value) ? 'array' : 'system')
                            ->setName($this->setting->getSetting()->getName() . '.' . strtolower($name));
                        $this->writeSettingCache($setting, $this->setting->getSetting()->getName() . '.' . strtolower($name));
                        $this->setting->setValue($value);
                        $this->setting->setBaseSetting(false);
                        return $this;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @return Setting|null
     * @throws TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function findOneByName($name): ?Setting
    {
        try {
            return $this->getSettingRepository()->findOneByName(strtolower($name));
        } catch (TableNotFoundException $e) {
            if (in_array($e->getErrorCode(), ['1146', '1045']))
                return null;
            throw $e;
        }
    }

    /**
     * getSettingRepository
     *
     * @return SettingRepository
     */
    private function getSettingRepository(): SettingRepository
    {
        return $this->getEntityManager()->getRepository(Setting::class);
    }

    /**
     * getEntityManager
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * writeSettingCache
     *
     * @param Setting $setting
     * @return SettingManager
     */
    private function writeSettingCache(Setting $setting, $name = null): SettingManager
    {
        $this->setting = new SettingCache();
        $this->setting->setSetting($setting);
        $this->setting->setName($name ?: $setting->getName());
        $this->setting->setCacheTime(new \DateTime('now'));
        $this->addSetting($this->setting, $name ?: $setting->getName());
        if ($this->hasSession())
            $this->getSession()->set('settings', $this->settings);
        return $this;
    }

    /**
     * addSetting
     *
     * @param SettingCache|null $setting
     * @param string $name
     * @return SettingManager
     */
    private function addSetting(?SettingCache $setting, string $name): SettingManager
    {
        if (empty($setting) || $this->getSettings()->containsKey(strtolower($name)))
            return $this;
        $this->getSettings()->set(strtolower($name), $setting);

        return $this;
    }

    /**
     * Get parameter
     *
     * @param   string $name
     * @param   mixed $default
     *
     * @return  mixed
     */
    public function getParameter($name, $default = null)
    {
        if ($this->hasParameter($name))
            return $this->getContainer()->getParameter($name);

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
     * @param   mixed $default
     *
     * @return  mixed
     */
    public function hasParameter($name)
    {
        return $this->getContainer()->hasParameter($name);
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     */
    public function handleImportRequest(FormInterface $form)
    {
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('import_file')->getData();
            try {
                $data = Yaml::parse(file_get_contents($file->getPathName()));
            } catch (\Exception $e) {
                $this->getMessageManager()->add('danger', 'setting.import.import.error', ['%{message}' => $e->getMessage()], 'Setting');
                return;
            }

            if ($data['name'] !== $form->get('import_file')->getData()->getClientOriginalName()) {
                $this->messageManager->add('danger', 'setting.import.name.error', ['%{name}' => $data['name']], 'Setting');
                return;
            }

            $this->loadSettings($data['settings'], $data['name']);
        }
    }

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        $this->messageManager->setDomain('System');
        return $this->messageManager;
    }

    /**
     * find
     *
     * @param $id
     * @return Setting
     */
    public function find($id): Setting
    {
        $setting = $this->getSettingRepository()->find($id);
        if ($setting instanceof Setting)
            $this->clearSetting($setting);
        return $setting;
    }

    /**
     * clearSetting
     *
     * @param Setting $setting
     * @return SettingManager
     */
    public function clearSetting(Setting $setting): SettingManager
    {
        if ($setting->getName()) {
            if ($this->getSettings()->containsKey($setting->getName()))
                $this->getSettings()->remove($setting->getName());
        }
        $this->setting = null;
        if ($this->hasSession())
            $this->getSession()->set('settings', $this->settings);

        return $this;
    }

    /**
     * set Setting
     *
     * @version 31st October 2016
     * @since   21st October 2016
     *
     * @param   string $name
     * @param   mixed $value
     *
     * @return  mixed
     * @throws \Exception
     */
    public function set($name, $value): SettingManager
    {
        $name = strtolower($name);
        $this->get($name);

        if (is_null($this->setting) || empty($this->setting->getSetting()->getName()))
            return $this;

        if (!$this->isInstallMode())
            if (!$this->getAuthorisation()->isGranted($this->setting->getSetting()->getRole(), $this->setting->getSetting()))
                return $this;

        $setting = $this->find($this->setting->getSetting()->getId());
        $this->writeSettingCache($setting);
        $this->setting->setValue($value)
            ->setCacheTime(new \DateTime('now'));

        try {
            $this->getEntityManager()->persist($this->setting->getSetting());
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        $this->flushToSession();

        return $this;
    }

    /**
     * @var bool
     */
    private $installMode = false;

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
     * @return AuthorizationCheckerInterface
     */
    public function getAuthorisation(): AuthorizationCheckerInterface
    {
        return $this->authorisation;
    }

    /**
     * flushToSession
     *
     * @param Setting $setting
     */
    private function flushToSession(): SettingManager
    {
        if ($this->hasSession())
            $this->getSession()->set('settings', $this->settings);
        return $this;
    }

    /**
     * @return bool
     */
    private function isFlip(): bool
    {
        return $this->flip;
    }

    /**
     * getCurrentSetting
     *
     * @return Setting|null
     */
    public function getCurrentSetting(): ?Setting
    {
        if (!$this->setting)
            return null;
        return $this->setting->getSetting();
    }

    /**
     * @param $data
     * @param $resource
     */
    private function loadSettings(array $data = [], string $resource)
    {
        if (empty($data)) {
            $this->messageManager->addMessage('warning', 'setting.resource.settings.missing', ['%{resource}' => $resource], 'System');
            return;
        }
        $count = 0;
        foreach ($data as $name => $datum) {
            $setting = $this->findOneByName($name);
            if ($setting instanceof Setting) {
                switch ($setting->getType()) {
                    case 'time':
                        $this->set($name, new \DateTime('1970-01-01 ' . $datum));
                        break;
                    default:
                        $this->set($name, $datum);
                }
                $count++;
            } else {
                $this->messageManager->addMessage('warning', 'setting.resource.missing', ['%{name}' => $name], 'System');
            }
        }

        $this->messageManager->addMessage('success', 'setting.resource.success', ['{{name}}' => $resource, "%{count}" => $count], 'System');
    }

    /**
     * getValue
     *
     * @param string $name
     * @param null $default
     * @param array $options
     * @return array|mixed|null|string
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    private function getValue(string $name, $default = null, $options = [])
    {
        switch ($this->setting->getSetting()->getType()) {
            case 'twig':
                $value = null;
                try {
                    return $this->twig->getTwig()->createTemplate($this->setting->getValue())->render($options);
                } catch (\Twig_Error_Syntax $e) {
                    throw $e;
                } catch (\Twig_Error_Runtime $e) {
                    // Ignore Runtime Errors, and return raw twig value
                    return $this->setting->getValue();
                }
                return $value ?: $this->setting->getValue();
                break;
            case 'array':
                if ($this->flip)
                    return array_flip($this->setting->getValue());
                return $this->setting->getValue();
                break;
            default:
                return $this->setting->getValue();
        }
        return null;
    }

    /**
     * createSettings
     * @param array $data
     * @return int
     * @throws TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function createSettings(array $data = []): int
    {

        $resolver = new OptionsResolver();
        $resolver->setRequired(
            [
                'name',
                'displayName',
                'type',
                'description',
            ]
        );
        $resolver->setDefaults(
            [
                'value' => null,
                'defaultValue' => null,
                'role' => null,
                'choice' => null,
                'validator' => null,
            ]
        );

        $create = $this->getRequest()->request->get('create');
        $data = $create ? Yaml::parse($create['setting']) : $data;
        $count = 0;
        foreach ($data as $name => $values) {
            $values['name'] = strtolower($name);
            $values = $resolver->resolve($values);
            if ($this->has($name))
                $setting = $this->findOneByName($name);
            else
                $setting = new Setting();
            foreach ($values as $field => $value) {
                $func = 'set' . ucfirst($field);
                $setting->$func($value);
            }
            $setting->convertImportValues();
            $this->getEntityManager()->persist($setting);
            $this->getEntityManager()->flush();
            $count++;
        }
        $this->getMessageManager()->add('success', 'setting.create.success', ['transChoice' => $count], 'System');
        return $count;
    }
}