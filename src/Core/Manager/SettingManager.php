<?php
namespace App\Core\Manager;

use App\Core\Organism\SettingCache;
use App\Entity\Setting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
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
     * @param MessageManager $messageManager
     */
    public function __construct(ContainerInterface $container, MessageManager $messageManager, AuthorizationCheckerInterface $authorisation, TwigManager $twig)
    {
        $this->setContainer($container);
        $this->messageManager = $messageManager;
        $this->authorisation = $authorisation;
        $this->twig = $twig;
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
     * get
     *
     * @param string $name
     * @param null $default
     * @param array $options
     * @return array|mixed|null|string
     * @throws \Doctrine\DBAL\Exception\TableNotFoundException
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public function get(string $name, $default = null, array $options = [])
    {
        $name = strtolower($name);
        $this->readSession()->setName($name)->getSetting($name);

        if ($this->isValid())
            return $this->getValue($default, $options);

        $this->loadSetting($name);

        if ($this->isValid())
            return $this->getValue($default, $options);

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
     * @return ArrayCollection
     */
    public function getSettings(): ArrayCollection
    {

        if (empty($this->settings))
            $this->settings = new ArrayCollection();
        return $this->settings;
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

        if (empty($this->name) || mb_strpos($this->name, $name) !== 0) {
            $this->name     = $name;
            $this->setting  = null;
        }

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
     * @param SettingCache|null $setting
     * @return SettingManager
     */
    public function setSetting(?SettingCache $setting): SettingManager
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * loadSetting
     *
     * @param string $name
     * @return SettingManager
     * @throws \Doctrine\DBAL\Exception\TableNotFoundException
     */
    private function loadSetting(string $name): SettingManager
    {
        $setting = $this->findOneByName($name);

        if ($setting instanceof SettingCache)
            return $this->addSetting($setting);
        $parts = explode('.', $name);

        if (count($parts) > 1) {
            $part = array_pop($parts);
            $name = implode('.', $parts);
            $value = $this->get($name);

            if ($this->setting instanceof SettingCache && $this->setting->isValid()) {
                if ($this->setting->getType() !== 'array')
                    return $this;
                foreach ($this->setting->getValue() as $key => $value) {
                    if (strtolower($part) === strtolower($key)) {
                        $setting = $this->getSettingCache();
                        $setting->setType(is_array($value) ? 'array' : 'system')
                            ->setName($this->setting->getSetting()->getName() . '.' . strtolower($key))
                            ->setParent($this->setting->getSetting()->getName())
                            ->setParentKey($key)->setValue($value)
                            ->setDefaultValue(null)
                            ->setCacheTime(new \DateTime('now'));
                        $this->addSetting($setting);
                        return $this;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * findOneByName
     *
     * @param string $name
     * @return SettingCache|null
     * @throws \Doctrine\DBAL\Exception\TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function findOneByName(string $name): ?SettingCache
    {
        $setting = $this->getSettingCache();

        $this->addSetting($setting->findOneByName($name, $this->getEntityManager()));

        return $this->setting;
    }

    /**
     * getSettingCache
     *
     * @return SettingCache
     */
    private function getSettingCache(): SettingCache
    {
        return new SettingCache();
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
     * addSetting
     *
     * @param SettingCache|null $setting
     * @param string $name
     * @return SettingManager
     */
    private function addSetting(?SettingCache $setting, ?string $name = null): SettingManager
    {
        if (empty($setting) || ! $setting->isValid())
            return $this;

        $name = $name ?: $setting->getName();

        $this->getSettings()->set(strtolower($name), $setting);

        $this->setting = $setting;

        $this->flushToSession();

        return $this;
    }

    /**
     * isValid
     *
     * @return bool
     */
    private function isValid(): bool
    {
        if (! $this->setting instanceof SettingCache)
            return false;
        return $this->setting->isValid();
    }

    /**
     * has
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        $this->get($name);
        return $this->isValid();
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
            $setting = $this->getSettingCache();

            if ($setting->importSetting($values)) {
                $this->addSetting($setting, $name);
                $count++;
            }
        }
        $this->getMessageManager()->add('success', 'setting.create.success', ['transChoice' => $count], 'System');
        return $count;
    }

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * getMessageManager
     *
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
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
        $this->installMode = $installMode ? true : false ;
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
     * getValue
     *
     * @param null $default
     * @param array $options
     * @return array|mixed|null|string
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    private function getValue($default = null, array $options = [])
    {
        switch ($this->setting->getType()) {
            case 'twig':
                $value = null;
                try {
                    return $this->twig->getTwig()->createTemplate($this->setting->getFinalValue($default))->render($options);
                } catch (\Twig_Error_Syntax $e) {
                    throw $e;
                } catch (\Twig_Error_Runtime $e) {
                    // Ignore Runtime Errors, and return raw twig value
                    return $this->setting->getFinalValue($default);
                }
                break;
            case 'array':
                if ($this->isFlip())
                    return array_flip($this->setting->getFinalValue($default));
                return $this->setting->getFinalValue($default);
                break;
            default:
                return $this->setting->getFinalValue($default);
        }
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
     * @return bool
     */
    private function isFlip(): bool
    {
        return $this->flip;
    }

    /**
     * set Setting
     *
     * @version 31st October 2016
     * @since   21st October 2016
     * @param $name
     * @param $value
     * @return SettingManager
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public function set($name, $value): SettingManager
    {
        $name = strtolower($name);
        $this->get($name);

        if (!$this->isValid())
            return $this;

        if (!$this->isInstallMode())
            if (!$this->getAuthorisation()->isGranted($this->setting->getSetting()->getRole(), $this->setting->getSetting()))
                return $this;

        $this->setting = $this->getEntityManager()->getRepository(Setting::class)->find($this->setting->getId());

        $this->setting->setValue($value)
            ->setCacheTime(new \DateTime('now'))
            ->writeSetting($this->getEntityManager());

        return $this->addSetting($this->setting);
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
     * @return TwigManager
     */
    public function getTwig(): TwigManager
    {
        return $this->twig;
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

        return $this->flushToSession();
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
                $this->getMessageManager()->add('danger', 'setting.import.name.error', ['%{name}' => $data['name']], 'Setting');
                return;
            }

            $this->loadSettings($data['settings'], $data['name']);
        }
    }

    /**
     * find
     *
     * @param $id
     * @return Setting
     * @throws \Doctrine\DBAL\Exception\TableNotFoundException
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public function find($id): Setting
    {
        $setting = $this->getEntityManager()->getRepository(Setting::class)->find($id);
        if ($setting instanceof Setting)
            $this->get($setting->getName());
        return $this->getCurrentSetting();
    }

    /**
     * injectSetting
     *
     * @param Setting $setting
     * @return SettingManager
     */
    public function injectSetting(Setting $setting): SettingManager
    {
        $this->setting = $this->getSettingCache();
        $this->setting->setSetting($setting);
        $this->setting->setName($setting->getName());
        $this->setting->setCacheTime(new \DateTime('now'));
        $this->setting->setBaseSetting(true);
        return $this->addSetting($this->setting);
    }

    /**
     * translateValue
     *
     * @return array
     */
    public function translateValue(): MessageManager
    {
        $setting  = $this->setting;
        if ($setting->getTranslateChoice() === 'false')
        {
            $this->getMessageManager()->add('info', 'setting.translate.turned.off', [], 'System');
            return $this->getMessageManager();
        }

        switch ($setting->getType())
        {
            case 'array':
                $count = 0;
                foreach($this->generateTranslationKeys($setting->getName(), $setting->getValue()) as $name=>$value)
                {
                    $this->getMessageManager()->add('info', 'setting.translate.definition', ['%{name}' => $name, '%{value}' => $this->getTranslator()->trans($value, [], $setting->getTranslateChoice() ?: 'Setting')], 'System');
                    if ($count++ > 24)
                        return $this->getMessageManager();
                }
                break;
            default:
                $this->getMessageManager()->add('info', 'setting.translate.not.required', [], 'System');
        }
        return $this->getMessageManager();
    }

    /**
     * generateTranslationKeys
     *
     * @param $key
     * @param $data
     * @return array
     */
    public function generateTranslationKeys($key, $data)
    {
        $results = [];
        foreach($data as $name => $value)
        {
            dump([strval(intval($name)), trim($name)]);
            if (strval(intval($name)) !== trim($name))
                $results[$name] = $key. '.' . $name;
            if (is_array($value))
                $results = array_merge($results, $this->generateTranslationKeys($key, $value));
            else
                $results[$value] = $key. '.' . $value;
        }

        return $results;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->getContainer()->get('translator');
    }
}