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
use App\Repository\SettingRepository;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

class SettingCache
{
    /**
     * @var Setting
     */
    private $setting;

    /**
     * @return Setting|null
     */
    public function getSetting(): ?Setting
    {
        return $this->setting;
    }

    /**
     * @param Setting|null $setting
     * @return SettingCache
     */
    public function setSetting(?Setting $setting): SettingCache
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
     * @param string|null $name
     * @return SettingCache
     */
    public function setName(?string $name = null): SettingCache
    {
        $this->name = strtolower($name ?: $this->getSetting()->getName());
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
     * @var mixed
     */
    private $convertedValue;

    /**
     * @param null $default
     * @return mixed
     */
    public function getValue($default = null)
    {
        if (!empty($this->getConvertedValue()))
            return $this->getConvertedValue();
        $method = 'get' . ucfirst($this->getSetting()->getType()) . 'Value';
        $this->value = $this->$method();
        $this->defaultValue = $this->getDefaultValue();
        $value = $this->value ?: $this->defaultValue;
        $value = $value ?: $default;

        $this->setConvertedValue($value);
        return $value;
    }

    /**
     * @param mixed $value
     * @return SettingCache
     */
    public function setValue($value): SettingCache
    {
        $method = 'set' . ucfirst($this->getSetting()->getType()) . 'Value';
        $this->value = $value;
        $this->setConvertedValue($value);
        return $this->$method();
    }

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * getDefaultValue
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        $method = 'getDefault' . ucfirst($this->getSetting()->getType()) . 'Value';
        return $this->defaultValue = $this->$method();
    }

    /**
     * @return mixed
     */
    public function getConvertedValue()
    {
        return $this->convertedValue;
    }

    /**
     * @param mixed $convertedValue
     * @return SettingCache
     */
    public function setConvertedValue($convertedValue)
    {
        $this->convertedValue = $convertedValue;
        return $this;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * getSystemValue
     *
     * @param null $default
     * @return mixed|null
     */
    private function getSystemValue(): ?string
    {
        return $this->getStringValue();
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
    private function getArrayValue(): array
    {
        return self::convertDatabaseToArray($this->getSetting()->getValue());
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
    private function getImageValue(): ?string
    {
        return $this->getStringValue();
    }

    /**
     * getTwigValue
     *
     * @param null $default
     * @param array $options
     * @return mixed
     */
    private function getTwigValue(): ?string
    {
        return $this->getStringValue();
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
    private function getRegexValue(): ?string
    {
        return $this->getStringValue();
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
    private function getStringValue(): ?string
    {
        if ($this->getSetting()->getValue())
            return $this->getSetting()->getValue();
        return null;
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

        dump($value);
        $x = is_array(Yaml::parse($value));
        return is_array($x) ? $x : [];
    }

    /**
     * getDefaultImageValue
     *
     * @return null|string
     */
    private function getDefaultImageValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    /**
     * getDefaultSystemValue
     *
     * @return null|string
     */
    private function getDefaultSystemValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    private function getDefaultArrayValue(): ?array
    {
        return self::convertDatabaseToArray($this->getSetting()->getDefaultValue());
    }

    /**
     * getDefaultStringValue
     *
     * @param $default
     * @return null|string
     */
    private function getDefaultStringValue(): ?string
    {
        $value = $this->getSetting()->getDefaultValue();
        if (is_null($value) || is_string($value))
            return $value;
        return null;
    }

    /**
     * getDefaultTwigValue
     *
     * @return null|string
     */
    private function getDefaultTwigValue(): ?string
    {
        return $this->getDefaultStringValue();
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->getSetting()->getType();
    }

    /**
     * convertImportValues
     *
     * @return Setting
     */
    public function convertImportValues(): Setting
    {
        switch ($this->getType()){
            case 'time':
                $this->value = $this->value ? new \DateTime('1970-01-01 ' . $this->value) : null ;
                $this->defaultValue = $this->defaultValue ? new \DateTime('1970-01-01 ' . $this->defaultValue) : null ;
                break;
            default:
        }
        return $this->convertRawValues();
    }

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SettingCache constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * importSetting
     *
     * @param array $values
     * @return bool
     * @throws TableNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function importSetting(array $values): bool
    {
        $this->findOneByName($values['name']);
        $this->setting = $this->getSetting() instanceof Setting ? $this->getSetting() : new Setting();
        foreach ($values as $field => $value) {
            $func = 'set' . ucfirst($field);
            if ($field === 'value')
                $this->value = $value;
            elseif ($field === 'defaultValue')
                $this->defaultValue = $value;
            else
                $this->getSetting()->$func($value);
        }
        $this->convertImportValues();
        $this->getEntityManager()->persist($this->getSetting());
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * findOneByName
     *
     * @param string $name
     * @return SettingCache|null
     * @throws \Doctrine\ORM\ORMException
     */
    public function findOneByName(string $name): ?SettingCache
    {
        try {
            $this->setting = $this->getSettingRepository()->findOneByName(strtolower($name));
        } catch (TableNotFoundException $e) {
            if (in_array($e->getErrorCode(), ['1146', '1045']))
                $this->setting = null;
            throw $e;
        } catch (PDOException $e) {
            if (in_array($e->getErrorCode(), ['1146', '1045']))
                $this->setting = null;
            throw $e;
        } catch (\Exception $e) {
            if (in_array($e->getErrorCode(), ['1146', '1045']))
                $this->setting = null;
            throw $e;
        }
        return $this;
    }

    /**
     * getSettingRepository
     *
     * @return SettingRepository
     */
    private function getSettingRepository(): SettingRepository
    {
        return $this->getEntityManager()->getRepository(Setting::class);
    }
}