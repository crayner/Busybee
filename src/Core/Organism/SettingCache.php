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
        return strtolower($this->name);
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
     * @return null|\DateTime
     */
    public function getCacheTime(): ?\DateTime
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
        if ($this->getCacheTime()->getTimestamp() < strtotime('-20 minutes'))
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
        return $this->getStringValue($default);
    }

    /**
     * setSystemValue
     *
     * @return SettingCache
     */
    private function setSystemValue(): SettingCache
    {
        return $this->setStringValue();
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

        $this->value = $this->getSetting()->getValue();

        if ($this->value && is_array($this->value))
            return $this->value;

        $this->value = Yaml::parse($this->value);

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
        return $this->getStringValue($default);
    }

    /**
     * getTwigValue
     *
     * @param null $default
     * @param array $options
     * @return mixed
     */
    private function getTwigValue($default = null)
    {
        return $this->getStringValue($default);
    }

    /**
     * setTwigValue
     *
     * @return SettingCache
     */
    private function setTwigValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getRegexValue
     *
     * @param null $default
     * @return mixed
     */
    private function getRegexValue($default = null)
    {
        return $this->getStringValue($default);
    }

    /**
     * setRegexValue
     *
     * @return SettingCache
     */
    private function setRegexValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getStringValue
     *
     * @param null $default
     * @return mixed
     */
    private function getStringValue($default = null)
    {
        if ($this->value)
            return $this->value;
        return $this->value = $this->getSetting()->getValue() ?: $this->getDefaultValue($default);
    }

    /**
     * setStringValue
     *
     * @return SettingCache
     */
    private function setStringValue(): SettingCache
    {
        $this->getSetting()->setValue($this->value);
        return $this;
    }

    /**
     * getEnumValue
     *
     * @param null $default
     * @return mixed
     */
    private function getEnumValue($default = null)
    {
        return $this->getStringValue($default);
    }

    /**
     * setEnumValue
     *
     * @return SettingCache
     */
    private function setEnumValue(): SettingCache
    {
        return $this->setStringValue();
    }

    /**
     * getDateTimeValue
     *
     * @param null $default
     * @return \DateTime|null
     */
    private function getDateTimeValue($default = null): ?\DateTime
    {
        if ($this->value)
            return $this->value;
        return $this->value = unserialize($this->getSetting()->getValue() ?: $this->getDefaultValue($default));
    }

    /**
     * setDateTimeValue
     *
     * @return SettingCache
     */
    private function setDateTimeValue(): SettingCache
    {
        $this->getSetting()->setValue(serialize($this->value));
        return $this;
    }

    /**
     * getTimeValue
     *
     * @param null $default
     * @return \DateTime|null
     */
    private function getTimeValue($default = null): ?\DateTime
    {
        return $this->getDateTimeValue($default);
    }

    /**
     * setTimeValue
     *
     * @return SettingCache
     */
    private function setTimeValue(): SettingCache
    {
        return $this->setDateTimeValue();
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
        return $value;
    }

    /**
     * convertDateTimeFromDataBase
     *
     * @param $value
     * @return \DateTime|null
     */
    public static function convertDatabaseToDateTime($value): ?\DateTime
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

    /**
     * convertArrayToDatabase
     *
     * @param $value
     * @return null|string
     */
    public static function convertArrayToDatabase($value): ?string
    {
        if (empty($value))
            return null;
        if (is_array($value))
            return Yaml::dump($value);
        return $value;
    }

    /**
     * convertDatabaseToArray
     *
     * @param $value
     * @return array
     */
    public static function convertDatabaseToArray($value): array
    {
        if (empty($value))
            return [];
        if (is_array($value))
            return $value;

        return Yaml::parse($value);
    }

    /**
     * getDefaultImageValue
     *
     * @return null|string
     */
    private function getDefaultImageValue($default = null): ?string
    {
        return $this->getDefaultStringValue($default);
    }

    /**
     * getDefaultStringValue
     *
     * @param $default
     * @return null|string
     */
    private function getDefaultStringValue($default): ?string
    {
        $value = $this->getSetting()->getDefaultValue() ?: $default;
        if (is_null($value) || is_string($value))
            return $value;
        return null;
    }
}