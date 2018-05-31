<?php
namespace App\Core\Manager;

trait TabManagerTrait
{
    /**
     * Use this method as a callable to test if the tab is to be displayed.
     * @param string|array $method
     * @return bool
     */
    public function isDisplay($method = []): bool
    {
 dump($method);
        if (is_string($method)) {
            if (empty($method))
                return true;
            if (method_exists($this, $method))
                return $this->$method();

            return false;
        }
        if (is_string($method['method']) && is_array($method['with']))
        {
            if (method_exists($this, $method['method'])) {
                $func = $method['method'];
                return $this->$func($method['with']);
            }
            return false;
        }
        throw new \InvalidArgumentException('The arguments passed to the tab display test must be an array = [method => name, with => [parameterArray]]');
    }

}