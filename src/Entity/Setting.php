<?php
namespace App\Entity;

use App\Core\Exception\Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Yaml\Yaml;

/**
 * Setting
 */
class Setting
{
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
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

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
	 * @var \App\Entity\User
	 */
	private $createdBy;
	/**
	 * @var \App\Entity\User
	 */
	private $modifiedBy;
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
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return Setting
	 */
	public function setType($type)
	{
		$this->type = $type;

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
		$type = 'get' . ucfirst($this->getType());

		return $this->$type();
	}

	/**
	 * Set value
	 *
	 * @param blob $value
	 *
	 * @return blob
	 */
	public function setValue($value)
	{

		if (empty($this->getType()))
			throw new Exception('The setting ' . $this->getName() . ' has not set the type correctly.');

		$type = 'set' . ucfirst($this->getType());


		return $this->$type($value);
	}

	/**
	 * Get lastModified
	 *
	 * @return \DateTime
	 */
	public function getLastModified()
	{
		return $this->lastModified;
	}

	/**
	 * Set lastModified
	 *
	 * @param \DateTime $lastModified
	 *
	 * @return Setting
	 */
	public function setLastModified($lastModified)
	{
		$this->lastModified = $lastModified;

		return $this;
	}

	/**
	 * Get createdOn
	 *
	 * @return \DateTime
	 */
	public function getCreatedOn()
	{
		return $this->createdOn;
	}

	/**
	 * Set createdOn
	 *
	 * @param \DateTime $createdOn
	 *
	 * @return Setting
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get role
	 *
	 * @return string
	 */
	public function getRole()
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
	 * @param \Busybee\Core\SecurityBundle\Entity\Role $role
	 *
	 * @return Setting
	 */
	public function setSecurityActive($sa = true)
	{
		$this->securityActive = $sa;

		return $this;
	}

	/**
	 * Get createdBy
	 *
	 * @return \App\Entity\User
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	/**
	 * Set createdBy
	 *
	 * @param \App\Entity\User $createdBy
	 *
	 * @return Setting
	 */
	public function setCreatedBy(\App\Entity\User $createdBy = null)
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * Get modifiedBy
	 *
	 * @return \App\Entity\User
	 */
	public function getModifiedBy()
	{
		return $this->modifiedBy;
	}

	/**
	 * Set modifiedBy
	 *
	 * @param \App\Entity\User $modifiedBy
	 *
	 * @return Setting
	 */
	public function setModifiedBy(\App\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

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
	 * @return array
	 */
	private function getArray(): array
	{
		return Yaml::parse($this->value);
	}

	/**
	 * @param $value
	 *
	 * @return Setting
	 */
	private function setArray($value): Setting
	{
		if (is_array($value))
			$value = Yaml::dump($value);

		$this->value = $value;

		return $this;
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
	 * @return Setting
	 */
	private function setImage($value): Setting
	{
		if ($value instanceof UploadedFile)
			throw new Exception('The image must first be converted from an uploaded file.');
		if ($value instanceof File)
			$value = $value->getPathname();

		$this->value = $value;

		return $this;
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
	private function setInteger($value): Setting
	{
		$this->value = intval($value);

		return $this;
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
	 * @return Setting
	 */
	private function setTwig($value): Setting
	{
		$this->value = $value;

		return $this;
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
	 * @return Setting
	 */
	private function setString($value): Setting
	{
		$this->value = $value;

		return $this;
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
	 * @return Setting
	 */
	private function setText($value): Setting
	{
		$this->value = $value;

		return $this;
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
	 * @return Setting
	 */
	private function setTime($value): Setting
	{
		if ($value instanceof \DateTime)
			$this->value = serialize($value);
		else
			$this->value = empty($value) ? null : $value;

		$this->getTime();

		return $this;
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
	 * @return Setting
	 */
	private function setRegex($value): Setting
	{
		if (false === @preg_match($value, 'jsfoieqwht9rhewtgs euohgt')) // if this valid regex
			$value = null;

		$this->value = $value;

		return $this;
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
	public function setSystem($value): Setting
	{
		// It really is up to the programmer to check this....
		$this->value = $value;

		return $this;
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
