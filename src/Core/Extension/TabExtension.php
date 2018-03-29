<?php
namespace App\Core\Extension;

use App\Core\Exception\MissingClassException;
use App\Core\Manager\TabManagerInterface;
use Twig\Extension\AbstractExtension;

class TabExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('setTabManager', array($this, 'setTabManager')),
            new \Twig_SimpleFunction('getTabs', array($this, 'getTabs')),
            new \Twig_SimpleFunction('getResetScripts', array($this, 'getResetScripts')),
        );
    }

    /**
     * @var TabManagerInterface
     */
    private $tabManager;

    /**
     * @param TabManagerInterface $tabManager
     * @return TabExtension
     */
    public function setTabManager(TabManagerInterface $tabManager)
    {
        $this->tabManager = $tabManager;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return $this->getTabManager()->getTabs();
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        return $this->getTabManager()->getResetScripts();
    }

    /**
     * @return TabManagerInterface
     * @throws MissingClassException
     */
    private function getTabManager(): TabManagerInterface
    {
        if (! $this->tabManager instanceof TabManagerInterface)
            throw new MissingClassException('The tabManager is not loaded in the extension.  Load the manager in the master twig template using {% set x = setTabManager(manager) %} where the manager is passed to the template from the controller.');
        return $this->tabManager;
    }
}