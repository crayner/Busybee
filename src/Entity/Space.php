<?php
namespace App\Entity;

use App\School\Entity\SpaceExtension;
use Hillrange\Security\Entity\User;

/**
 * Space
 */
class Space extends SpaceExtension
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
	private $type;

	/**
	 * @var integer
	 */
	private $capacity;

	/**
	 * @var boolean
	 */
	private $computer;

	/**
	 * @var integer
	 */
	private $studentComputers;

	/**
	 * @var boolean
	 */
	private $projector;

	/**
	 * @var boolean
	 */
	private $tv;

	/**
	 * @var boolean
	 */
	private $dvd;

	/**
	 * @var boolean
	 */
	private $hifi;

	/**
	 * @var boolean
	 */
	private $speakers;

	/**
	 * @var boolean
	 */
	private $iwb;

	/**
	 * @var string
	 */
	private $phoneInt;

	/**
	 * @var string
	 */
	private $phoneExt;

	/**
	 * @var string
	 */
	private $comment;

	/**
	 * @var Staff
	 */
	private $staff;

	/**
	 * @var Campus
	 */
	private $campus;

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
	 * Set id
	 *
	 * @return Space
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
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
	 * @return Space
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
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
	 * @return Space
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get capacity
	 *
	 * @return integer
	 */
	public function getCapacity()
	{
		return $this->capacity;
	}

	/**
	 * Set capacity
	 *
	 * @param integer $capacity
	 *
	 * @return Space
	 */
	public function setCapacity($capacity)
	{
		$this->capacity = $capacity;

		return $this;
	}

	/**
	 * Get computer
	 *
	 * @return boolean
	 */
	public function getComputer()
	{
		return $this->computer;
	}

	/**
	 * Set computer
	 *
	 * @param boolean $computer
	 *
	 * @return Space
	 */
	public function setComputer($computer)
	{
		$this->computer = $computer;

		return $this;
	}

	/**
	 * Get studentComputers
	 *
	 * @return integer
	 */
	public function getStudentComputers()
	{
		return $this->studentComputers;
	}

	/**
	 * Set studentComputers
	 *
	 * @param integer $studentComputers
	 *
	 * @return Space
	 */
	public function setStudentComputers($studentComputers)
	{
		$this->studentComputers = $studentComputers;

		return $this;
	}

	/**
	 * Get projector
	 *
	 * @return boolean
	 */
	public function getProjector()
	{
		return $this->projector;
	}

	/**
	 * Set projector
	 *
	 * @param boolean $projector
	 *
	 * @return Space
	 */
	public function setProjector($projector)
	{
		$this->projector = $projector;

		return $this;
	}

	/**
	 * Get tv
	 *
	 * @return boolean
	 */
	public function getTv()
	{
		return $this->tv;
	}

	/**
	 * Set tv
	 *
	 * @param boolean $tv
	 *
	 * @return Space
	 */
	public function setTv($tv)
	{
		$this->tv = $tv;

		return $this;
	}

	/**
	 * Get dvd
	 *
	 * @return boolean
	 */
	public function getDvd()
	{
		return $this->dvd;
	}

	/**
	 * Set dvd
	 *
	 * @param boolean $dvd
	 *
	 * @return Space
	 */
	public function setDvd($dvd)
	{
		$this->dvd = $dvd;

		return $this;
	}

	/**
	 * Get hifi
	 *
	 * @return boolean
	 */
	public function getHifi()
	{
		return $this->hifi;
	}

	/**
	 * Set hifi
	 *
	 * @param boolean $hifi
	 *
	 * @return Space
	 */
	public function setHifi($hifi)
	{
		$this->hifi = $hifi;

		return $this;
	}

	/**
	 * Get speakers
	 *
	 * @return boolean
	 */
	public function getSpeakers()
	{
		return $this->speakers;
	}

	/**
	 * Set speakers
	 *
	 * @param boolean $speakers
	 *
	 * @return Space
	 */
	public function setSpeakers($speakers)
	{
		$this->speakers = $speakers;

		return $this;
	}

	/**
	 * Get iwb
	 *
	 * @return boolean
	 */
	public function getIwb()
	{
		return $this->iwb;
	}

	/**
	 * Set iwb
	 *
	 * @param boolean $iwb
	 *
	 * @return Space
	 */
	public function setIwb($iwb)
	{
		$this->iwb = $iwb;

		return $this;
	}

	/**
	 * Get phoneInt
	 *
	 * @return string
	 */
	public function getPhoneInt()
	{
		return $this->phoneInt;
	}

	/**
	 * Set phoneInt
	 *
	 * @param string $phoneInt
	 *
	 * @return Space
	 */
	public function setPhoneInt($phoneInt)
	{
		$this->phoneInt = $phoneInt;

		return $this;
	}

	/**
	 * Get phoneExt
	 *
	 * @return string
	 */
	public function getPhoneExt()
	{
		return $this->phoneExt;
	}

	/**
	 * Set phoneExt
	 *
	 * @param string $phoneExt
	 *
	 * @return Space
	 */
	public function setPhoneExt($phoneExt)
	{
		$this->phoneExt = $phoneExt;

		return $this;
	}

	/**
	 * Get comment
	 *
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Space
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;

		return $this;
	}

	/**
	 * Get campus
	 *
	 * @return Campus
	 */
	public function getCampus()
	{
		return $this->campus;
	}

	/**
	 * Set campus
	 *
	 * @param Campus $campus
	 *
	 * @return Space
	 */
	public function setCampus(Campus $campus = null)
	{
		$this->campus = $campus;

		return $this;
	}

	/**
	 * Get staff
	 *
	 * @return Staff
	 */
	public function getStaff()
	{
		return $this->staff;
	}

	/**
	 * Set staff
	 *
	 * @param Staff $staff
	 *
	 * @return Space
	 */
	public function setStaff(Staff $staff = null)
	{
		$this->staff = $staff;

		return $this;
	}
}
