<?php
namespace App\Core\Manager;

interface TabManagerInterface
{
    /**
     * get Tabs
     *
     * An array of tabs.
     * Each tab to consist of:
     *   label: The label of the tab
     *   include: The Twig script to populate the tab
     *   message: a unique ID to set for ajax messages to populate
     *   translation: Translation Domain
     *
     * @return array
     */
    public function getTabs(): array;

    /**
     * @return string
     */
    public function getResetScripts(): string;

    /**
     * @param string $method
     * @return bool
     */
    public function isDisplay(string $method = ''): bool;
}