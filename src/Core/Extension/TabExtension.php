<?php
namespace App\Core\Extension;

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
        return $this->tabManager->getTabs();
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        return $this->tabManager->getResetScripts();
    }
}