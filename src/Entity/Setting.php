<?php
namespace App\Entity;

use App\Core\Organism\SettingCache;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

/**
 * Setting
 */
class Setting implements UserTrackInterface
{
	use UserTrackTrait;

    /**
     * @var int:null
     */
	private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Setting
     */
    public function setId(int $id): Setting
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var string|null
     */
    private $type;

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     * @return Setting
     */
    public function setType(?string $type): Setting
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @var string|null
     */
    private $name;

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return Setting
     */
    public function setName(?string $name): Setting
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var string|null
     */
    private $displayName;

    /**
     * @return null|string
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @param null|string $displayName
     * @return Setting
     */
    public function setDisplayName(?string $displayName): Setting
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @var string|null
     */
    private $description;

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return Setting
     */
    public function setDescription(?string $description): Setting
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @var mixed|null
     */
    private $value;

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed|null $value
     * @return Setting
     */
    public function setValue($value): Setting
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @var string|null
     */
    private $choice;

    /**
     * @return null|string
     */
    public function getChoice(): ?string
    {
        return $this->choice;
    }

    /**
     * @return bool
     */
    public function hasChoice(): bool
    {
        return $this->choice ? true : false ;
    }

    /**
     * @param null|string $choice
     * @return Setting
     */
    public function setChoice(?string $choice): Setting
    {
        $this->choice = $choice;
        return $this;
    }

    /**
     * @var string|null
     */
    private $validator;

    /**
     * @return null|string
     */
    public function getValidator(): ?string
    {
        return $this->validator;
    }

    /**
     * @param null|string $validator
     * @return Setting
     */
    public function setValidator(?string $validator): Setting
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @var string|null
     */
    private $role;

    /**
     * @return null|string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param null|string $role
     * @return Setting
     */
    public function setRole(?string $role): Setting
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     * @return Setting
     */
    public function setDefaultValue($defaultValue): Setting
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @var null
     */
    private $rawValue;

    /**
     * getRawValue
     *
     * @return mixed|null
     */
    public function getRawValue()
    {
        switch($this->getType()){
            case 'time':
                return $this->value = SettingCache::convertDatabaseToDateTime($this->getValue());
                break;
            case 'array':
                return $this->value = SettingCache::convertDatabaseToArray($this->getValue());
                break;
            default:
                return $this->value;
        }
    }

    /**
     * setRawValue
     *
     * @param $value
     * @return Setting
     */
    public function setRawValue($value): Setting
    {
        switch($this->getType()){
            case 'time':
                $this->value = SettingCache::convertDateTimeToDataBase($value);
                break;
            case 'array':
                $this->value = SettingCache::convertArrayToDatabase($value);
                break;
            default:
                $this->value = $value;
        }
        return $this;
    }

    /**
     * @var null
     */
    private $rawDefaultValue;

    /**
     * getRawValue
     *
     * @return mixed|null
     */
    public function getRawDefaultValue()
    {
        switch($this->getType()){
            case 'time':
                return $this->defaultValue = SettingCache::convertDatabaseToDateTime($this->getDefaultValue());
                break;
            case 'array':
                return $this->defaultValue = SettingCache::convertDatabaseToArray($this->getDefaultValue());
                break;
            default:
                return $this->defaultValue;
        }
    }

    /**
     * setRawValue
     *
     * @param $value
     * @return Setting
     */
    public function setRawDefaultValue($value): Setting
    {
        switch($this->getType()){
            case 'time':
                $this->defaultValue = SettingCache::convertDateTimeToDataBase($value);
                break;
            case 'array':
                $this->defaultValue = SettingCache::convertArrayToDataBase($value);
                break;
            default:
                $this->defaultValue = $value;
        }
        return $this;
    }

    /**
     * convertRawValues
     *
     * @return Setting
     */
    public function convertRawValues(): Setting
    {
        $this->setRawValue($this->getValue());
        $this->setRawDefaultValue($this->getDefaultValue());
        return $this;
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
     * @var bool
     */
    private $valid = true;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     * @return Setting
     */
    public function setValid(bool $valid): Setting
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @var string|null
     */
    private $translateChoice;

    /**
     * @return null|string
     */
    public function getTranslateChoice(): ?string
    {
        return $this->translateChoice;
    }

    /**
     * @param null|string $translateChoice
     * @return Setting
     */
    public function setTranslateChoice(?string $translateChoice): Setting
    {
        $this->translateChoice = $translateChoice;
        return $this;
    }
}
