<?php
namespace App\Core\Manager;

use App\Core\Organism\SettingCache;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * SettingManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
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
     * @return null
     */
    public function get(string $name, $default = null, array $options = [])
    {
        $name = strtolower($name);
        if ($this->readSession()->setName($name)->getSetting($name))
            return $this->getValue($default, $options);

        $this->loadSetting($name, $default, $options);

        if ($this->isSettingExists($name))
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
     * @param null $default
     * @param array $options
     * @return SettingManager
     * @throws TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    private function loadSetting(string $name, $default = null, array $options = []): SettingManager
    {
        $setting = $this->findOneByName($name);

        if ($setting instanceof SettingCache)
            return $this->addSetting($setting);

        $parts = explode('.', $name);

        if (count($parts) > 1) {
            $part = array_pop($parts);
            $name = implode('.', $parts);
            $value = $this->get($name, $default, $options);

            if (! empty($this->setting) && $this->setting instanceof SettingCache && $this->setting->getName() === $name) {
                if ($this->setting->getType() !== 'array')
                    return $this;
                if ($name === 'org.name'){
                    dump($value);
                    dump($this);
                    dump($this->setting->getValue());
                }
                foreach ($this->setting->getValue() as $key => $value) {
                    if (strtolower($part) === strtolower($key)) {
                        $setting = new Setting();
                        $setting->setType(is_array($value) ? 'array' : 'system')
                            ->setName($this->setting->getSetting()->getName() . '.' . strtolower($key));
                        $this->writeSettingCache($setting, $this->setting->getSetting()->getName() . '.' . strtolower($key));
                        $this->setting->setValue($value);
                        $this->setting->setBaseSetting(false);

                        return $this;
                    }
                }
            }
        }

        return $this;
    }
}