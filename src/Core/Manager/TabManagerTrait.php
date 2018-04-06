<?php
namespace App\Core\Manager;

trait TabManagerTrait
{
    /**
     * Use this method as a callable to test if the tab is to be displayed.
     * @param string $method
     * @return bool
     */
    public function isDisplay(string $method = ''): bool
    {
        if (empty($method))
            return true;
        if (method_exists($this, $method))
            return $this->$method();

        return false;
    }

}