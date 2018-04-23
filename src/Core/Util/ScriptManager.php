<?php
namespace App\Core\Util;


class ScriptManager
{
    /**
     * @var array
     */
    private $scripts;

    /**
     * @return array
     */
    public function getScripts(): array
    {
        return $this->scripts ?: [];
    }

    /**
     * @param array $scripts
     * @return ScriptManager
     */
    public function setScripts(array $scripts): ScriptManager
    {
        $this->scripts = $scripts;
        return $this;
    }

    /**
     * @param string $name
     * @return ScriptManager
     */
    public function addScript(string $name): ScriptManager
    {
        $this->scripts[] = $name;
        return $this;
    }
}