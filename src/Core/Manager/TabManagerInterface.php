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
     *   with: {array} added to the include. [optional]
     *   message: a unique ID to set for ajax messages to populate. [optional]
     *   translation: Translation Domain. [optional]
     *   display: A method name in the manager that returns bool. [optional]
     *
     * @return array
     */
    public function getTabs(): array;

    /**
     * @return string
     */
    public function getResetScripts(): string;

    /**
     * Use this method as a callable to test if the tab is to be displayed.
     * @param string $method
     * @return bool
     */
    public function isDisplay(string $method = ''): bool;
}