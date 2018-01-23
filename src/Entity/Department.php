<?php
namespace App\Entity;

use App\School\Entity\DepartmentExtension;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Department
 */
class Department extends DepartmentExtension
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
	 * @var string
	 */
	private $nameShort;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $members;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $courses;

	/**
	 * @var string
	 */
	private $importIdentifier;

	/**
	 * @var string
	 */
	private $blurb;

	/**
	 * @var string
	 */
	private $logo;

	/**
	 * Department constructor.
	 */
	public function __construct()
	{
		$this->members = new ArrayCollection();
		$this->courses = new ArrayCollection();
	}

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
	 * @return Department
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
	 * @return Department
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get nameShort
	 *
	 * @return string
	 */
	public function getNameShort()
	{
		return $this->nameShort;
	}

	/**
	 * Set nameShort
	 *
	 * @param string $nameShort
	 *
	 * @return Department
	 */
	public function setNameShort($nameShort)
	{
		$this->nameShort = $nameShort;

		return $this;
	}

	/**
	 * Add member
	 *
	 * @param DepartmentMember $member
	 *
	 * @return Department
	 */
	public function addMember(DepartmentMember $member): Department
	{
		$member->setDepartment($this);

		if ($this->members->contains($member))
			return $this;

		$this->members->add($member);

		return $this;
	}

	/**
	 * Remove member
	 *
	 * @param DepartmentMember $member
	 */
	public function removeMember(DepartmentMember $member): Department
	{
		$this->members->removeElement($member);

		return $this;
	}

	/**
	 * Get member
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getMembers($sorted = true)
	{
		if ($sorted)
			return $this->sortMembers();

		return $this->members;
	}

	/**
	 * Set member
	 *
	 * @return Department
	 */
	public function setMembers(ArrayCollection $members): Department
	{
		$this->members = $members;

		return $this;
	}

	/**
	 * Add course
	 *
	 * @param Course $course
	 *
	 * @return Department
	 */
	public function addCourse(Course $course)
	{
		if ($this->courses->contains($course))
			return $this;

		$this->courses->add($course);

		return $this;
	}

	/**
	 * Remove course
	 *
	 * @param Course $course
	 */
	public function removeCourse(Course $course)
	{
		$this->courses->removeElement($course);
	}

	/**
	 * Get courses
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCourses($sorted = true)
	{
		if ($sorted)
			return $this->sortCourses();

		return $this->courses;
	}

	/**
	 * Set courses
	 *
	 * @return Department
	 */
	public function setCourses(ArrayCollection $courses)
	{
		$this->courses = $courses;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getImportIdentifier(): ?string
	{
		return $this->importIdentifier;
	}

	/**
	 * @param string $importIdentifier
	 *
	 * @return Department
	 */
	public function setImportIdentifier(string $importIdentifier): Department
	{
		$this->importIdentifier = $importIdentifier;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBlurb(): ?string
	{
		return $this->blurb;
	}

	/**
	 * @param string $blurb
	 *
	 * @return Department
	 */
	public function setBlurb(string $blurb = null): Department
	{
		$this->blurb = $blurb;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogo(): ?string
	{
		return $this->logo;
	}

	/**
	 * @param string $logo
	 *
	 * @return Department
	 */
	public function setLogo(string $logo = null): Department
	{
		$this->logo = $logo;

		return $this;
	}
}
