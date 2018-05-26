<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 26/05/2018
 * Time: 08:05
 */
namespace App\Core\Organism;

use App\Entity\Setting;
use PhpParser\Node\Expr\Instanceof_;
use Symfony\Component\Yaml\Yaml;

class SettingCache
{
    /**
     * @var Setting
     */
    private $setting;

    /**
     * @return Setting
     */
    public function getSetting(): Setting
    {
        return $this->setting;
    }

    /**
     * @param Setting $setting
     * @return SettingCache
     */
    public function setSetting(Setting $setting): SettingCache
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return SettingCache
     */
    public function setName(string $name): SettingCache
    {
        $this->name = strtolower($name);
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $cacheTime;

    /**
     * @return \DateTime
     */
    public function getCacheTime(): \DateTime
    {
        return $this->cacheTime;
    }

    /**
     * @param \DateTime $cacheTime
     * @return SettingCache
     */
    public function setCacheTime(?\DateTime $cacheTime): SettingCache
    {
        $this->cacheTime = $cacheTime;
        return $this;
    }

    /**
     * isValidSetting
     *
     * @return bool
     */
    public function isValidSetting(): bool
    {
        if (empty($this->getCacheTime()))
            return false;
        if ($this->getCacheTime() < date('-20 minutes'))
        {
            $this->setCacheTime(null);
            return false;
        }
        return true;
    }

    /**
     * @var mixed
     */
    private $value;

    /**
     * @return mixed
     */
    public function getValue($default = null, array $options = [])
    {
        $method = 'get' . ucfirst($this->getSetting()->getType()) . 'Value';
        return $this->$method($default, $options);
    }

    /**
     * @param mixed $value
     * @return SettingCache
     */
    public function setValue($value)
    {
        $method = 'set' . ucfirst($this->getSetting()->getType()) . 'Value';
        $this->value = $value;
        return $this->$method();
    }

    /**
     * getDefaultValue
     *
     * @return mixed
     */
    public function getDefaultValue($default)
    {
        $method = 'getDefault' . ucfirst($this->getSetting()->getType()) . 'Value';
        return $this->$method($default);
    }
    /**
     * getSystemValue
     *
     * @param null $default
     * @return mixed|null
     */
    private function getSystemValue($default = null)
    {
        if ($this->value)
            return $this->value;
        return $this->value = $this->getSetting()->getValue() ?: $this->getDefaultValue($default);
    }

    /**
     * setSystemValue
     *
     * @return SettingCache
     */
    private function setSystemValue(): SettingCache
    {
        $this->getSetting()->setValue($this->value);
        return $this;
    }

    /**
     * getArrayValue
     *
     * @param null $default
     * @return array
     */
    private function getArrayValue($default = null): array
    {
        if ($this->value && is_array($this->value))
            return $this->value;

        $this->value = Yaml::parse($this->getSetting()->getValue());

        if (! is_array($this->value))
            $this->value = [];

        $default = $this->getDefaultValue($default);

        if (empty($this->value) && ! empty($default) && is_array($default))
            return $default;
        return $this->value;
    }

    /**
     * getDefaultArrayValue
     *
     * @param $default
     * @return array
     */
    private function getDefaultArrayValue($default): array
    {
        $value = Yaml::parse($this->getSetting()->getDefaultValue() ?: '');
        if (empty($default) && is_array($value))
            return $value;
        if (is_array($default))
            return $default;
        return [];
    }

    /**
     * settArrayValue
     *
     * @return SettingCache
     */
    private function setArrayValue(): SettingCache
    {
        $this->getSetting()->setValue(Yaml::dump($this->value));
        return $this;
    }
    /**
     * getSystemValue
     *
     * @param null $default
     * @return mixed|null
     */
    private function getImageValue($default = null)
    {
        if ($this->value)
            return $this->value;
        return $this->value = $this->getSetting()->getValue() ?: $this->getDefaultValue($default);
    }

    /**
     * @var bool
     */
    private $baseSetting = true;

    /**
     * @return bool
     */
    public function isBaseSetting(): bool
    {
        return $this->baseSetting;
    }

    /**
     * @param bool $baseSetting
     * @return SettingCache
     */
    public function setBaseSetting(bool $baseSetting): SettingCache
    {
        $this->baseSetting = $baseSetting;
        return $this;
    }

    /**
     * convertDateTimeToDataBase
     *
     * @param $value
     * @return null|string
     */
    public static function convertDateTimeToDataBase($value): ?string
    {
        if (empty($value))
            return null;
        if ($value Instanceof \DateTime)
            return serialize($value);
dump($value);
        return $value;
    }

    /**
     * convertDateTimeFromDataBase
     *
     * @param $value
     * @return \DateTime|null
     */
    public static function convertDateTimeFromDataBase($value): ?\DateTime
    {
        if (empty($value) || $value instanceof \DateTime)
            return $value;

        try {
            return unserialize($value);
        } catch (\ErrorException $e)
        {
            return null;
        }
    }
}