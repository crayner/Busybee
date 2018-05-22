<?php
namespace App\Entity;

use App\Core\Exception\Exception;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Yaml;

/**
 * Setting
 */
class Setting implements UserTrackInterface
{
	use UserTrackTrait;

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var blob
	 */
	private $value;

	/**
	 * @var boolean
	 */
	private $securityActive;

	/**
	 * @var string
	 */
	private $choice;

	/**
	 * @var string
	 */
	private $role;

	/**
	 * @var string
	 */
	private $description;
	/**
	 * @var string
	 */
	private $displayName;
	/**
	 * @var string
	 */
	private $validator;


	/**
	 * @var blob
	 */
	private $defaultValue;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType(): ?string
	{
		return strtolower($this->type);
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return Setting
	 */
	public function setType($type): Setting
	{
		$this->type = strtolower($type);

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return strtolower($this->name);
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Setting
	 */
	public function setName($name)
	{
		$this->name = strtolower($name);

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		if (empty($this->value) && ! empty($this->defaultValue))
			$this->value = $this->defaultValue;

		$type = 'get' . ucfirst($this->getType());

		return $this->$type();
	}

	/**
	 * Set value
	 *
	 * @param blob $value
	 *
	 * @return Setting
	 */
	public function setValue($value): Setting
	{
		if (empty($this->getType()))
			throw new Exception('The setting ' . $this->getName() . ' has not set the type correctly.');

		$type = 'set' . ucfirst($this->getType());

		$x = $this->$type($value);
		if ($x instanceof Setting)
			return $x;

		$this->value = $x;

		return $this;
	}

	/**
	 * Get role
	 *
	 * @return string
	 */
	public function getRole(): ?string
	{
		return $this->role;
	}

	/**
	 * Set role
	 *
	 * @param string $role
	 *
	 * @return Setting
	 */
	public function setRole($role)
	{
		$this->role = $role;

		return $this;
	}

	/**
	 * Get role
	 *
	 * @return boolean
	 */
	public function getSecurityActive()
	{
		return $this->securityActive;
	}

	/**
	 * Set role
	 *
	 * @param boole= $sa
	 *
	 * @return Setting
	 */
	public function setSecurityActive($sa = true)
	{
		$this->securityActive = $sa;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Setting
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get displayName
	 *
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * Set displayName
	 *
	 * @param string $displayName
	 *
	 * @return Setting
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;

		return $this;
	}

	/**
	 * Get choice
	 *
	 * @return string
	 */
	public function getChoice()
	{
		return $this->choice;
	}

	/**
	 * Set choice
	 *
	 * @param string $choice
	 *
	 * @return Setting
	 */
	public function setChoice($choice)
	{
		$this->choice = $choice;

		return $this;
	}

    /**
     * @return bool
     */
    public function hasChoice(): bool
    {
        if (empty($this->choice))
            return false;
        return true;
    }

	/**
	 * Get validator
	 *
	 * @return string
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 * Set validator
	 *
	 * @param string $validator
	 *
	 * @return Setting
	 */
	public function setValidator($validator)
	{
		$this->validator = $validator;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		switch ($this->type)
		{
			case 'array':
				return empty($this->defaultValue) ? [] : Yaml::parse($this->defaultValue);
			case 'image':
				return file_exists($this->defaultValue)? $this->defaultValue : null;
			case 'integer':
				return intval($this->defaultValue);
			case 'twig':
			case 'text':
			case 'string':
			case 'regex':
				return is_string($this->defaultValue) ? $this->defaultValue : null;
			case 'time':
				if (is_string($this->defaultValue) && preg_match("/^(2[0-3]|[01][0-9]):([0-5][0-9])$/", $this->defaultValue) == 1)
					$value = new \DateTime('1970-01-01 ' . $this->defaultValue . ':00');
				elseif (is_string($this->defaultValue))
					$value = unserialize($this->defaultValue);
				else
					$value = null;

				if (!$value instanceof \DateTime)
				{
					$value       = null;
					$this->defaultValue = null;
				}
				else
					$this->defaultValue = serialize($value);

				return $value;
			default:
				return $this->defaultValue;
		}
	}

	/**
	 * @param  $defaultValue
	 *
	 * @return Setting
	 */
	public function setDefaultValue($defaultValue): Setting
	{
		if (empty($this->getType()))
			throw new Exception('The setting ' . $this->getName() . ' has not set the type correctly.');

		$type = 'set' . ucfirst($this->getType());

		$x = $this->$type($defaultValue);
		if ($x instanceof Setting)
			return $x;

		$this->defaultValue = $x;

		return $this;
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
	 * @return array
	 */
	private function getArray(): array
	{
		if (empty($this->value))
			return [];
		return Yaml::parse($this->value);
	}

	/**
	 * @param $value
	 *
	 * @return
	 */
	private function setArray($value)
	{
		if (is_array($value))
			$value = Yaml::dump($value);

		return $value;
	}

	/**
	 * @return bool
	 */
	private function getBoolean(): bool
	{
		if (empty($this->value))
			$this->value = false;

		return $this->value ? true : false ;
	}

	/**
	 * @param bool|null $value
	 *
	 * @return bool
	 */
	private function setBoolean(bool $value = null): bool
	{
		if (empty($value))
			$value = false;

		return $value ? true : false ;
	}

	/**
	 * @return string
	 */
	private function getImage(): string
	{
		if (file_exists($this->value))
			return $this->value;

		return '';
	}

	/**
	 * @param $value
	 *
	 * @throws Exception
	 * @return
	 */
	private function setImage($value)
	{
		if ($value instanceof UploadedFile)
			throw new Exception('The image must first be converted from an uploaded file.');
		if ($value instanceof File)
			$value = $value->getPathname();

		return $value;
	}

	/**
	 * @return int
	 */
	private function getInteger(): int
	{
		return intval($this->value);
	}

	/**
	 * @return int
	 */
	private function setInteger($value): int
	{
		return intval($value);
	}

	/**
	 * @return string
	 */
	private function getTwig(): string
	{
		return is_string($this->value) ? $this->value : '{{ empty }}';
	}

	/**
	 * @param $value
	 *
	 * @return
	 */
	private function setTwig($value)
	{
		return $value;
	}

	/**
	 * @return string
	 */
	private function getString(): string
	{
		return is_string($this->value) ? $this->value : '';
	}

	/**
	 * @param $value
	 *
	 * @return
	 */
	private function setString($value)
	{
		return $value;
	}

	/**
	 * @return string
	 */
	private function getText(): string
	{
		return is_string($this->value) ? $this->value : '';
	}

	/**
	 * @param $value
	 *
	 * @return
	 */
	private function setText($value)
	{
		return $value;
	}

	/**
	 * @return null|\DateTime
	 */
	private function getTime(): ?\DateTime
	{
		if (is_string($this->value) && preg_match("/^(2[0-3]|[01][0-9]):([0-5][0-9])$/", $this->value) == 1)
			$value = new \DateTime('1970-01-01 ' . $this->value . ':00');
		elseif (is_string($this->value))
			$value = unserialize($this->value);
		else
			$value = null;


		if (!$value instanceof \DateTime)
		{
			$value       = null;
			$this->value = null;
		}
		else
			$this->value = serialize($value);

		return $value;
	}

	/**
	 * @param $value
	 *
	 * @return
	 */
	private function setTime($value)
	{
		if ($value instanceof \DateTime)
			$value = serialize($value);
		else
			$value = empty($value) ? null : $value;

		return $value;
	}

	/**
	 * @return null|string
	 */
	private function getRegex(): ?string
	{
		return empty($this->value) ? null : $this->value;
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	private function setRegex($value)
	{
		if (false === @preg_match($value, 'jsfoieqwht9rhewtgs euohgt')) // if this valid regex
			$value = null;

		return $value;
	}

	/**
	 * @return blob
	 */
	public function getSystem()
	{
		// It really is up to the programmer to check this....
		return $this->value;
	}

	/**
	 * @param $value
	 *
	 * @return Setting
	 */
	public function setSystem($value)
	{
		// It really is up to the programmer to check this....
		return $value;
	}

	/**
	 * @return mixed
	 */
	public function getEnum()
	{
		// It really is up to the programmer to check this....
		return $this->value;
	}

	/**
	 * @param $value
	 *
	 * @return Setting
	 */
	public function setEnum($value)
	{
		// It really is up to the programmer to check this....
		return $value;
	}

	public function __construct()
	{
		$this->setCreatedBy(null);
		$this->setModifiedBy(null);
	}

	public function getNameSelect()
	{
		return $this->getId();
	}

}
