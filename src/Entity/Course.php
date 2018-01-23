<?php
namespace App\Entity;
use App\School\Entity\CourseExtension;


/**
 * Course
 */
class Course extends CourseExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var string
	 */
	private $targetYear;

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
	 * Get code
	 *
	 * @return string
	 */
	public function getCode()
	{
		return strtoupper($this->code);
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Course
	 */
	public function setCode($code)
	{
		$this->code = strtoupper($code);

		return $this;
	}

	/**
	 * Get version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Set version
	 *
	 * @param string $version
	 *
	 * @return Course
	 */
	public function setVersion($version)
	{
		$this->version = $version;

		return $this;
	}

	/**
	 * Get targetYear
	 *
	 * @return string
	 */
	public function getTargetYear()
	{
		if (!is_array($this->targetYear))
			return explode(' ', $this->targetYear);

		return $this->targetYear;
	}

	/**
	 * Set targetYear
	 *
	 * @param string|array $targetYear
	 *
	 * @return Course
	 */
	public function setTargetYear($targetYear)
	{
		if (is_array($targetYear))
		{
			$targetYear = implode(' ', $targetYear);
		}

		$this->targetYear = $targetYear;

		return $this;
	}

	/**
	 * To String
	 *
	 * @return string
	 */
	public function __toString()
	{
		return strval($this->getName());
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Course
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}
}
