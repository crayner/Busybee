<?php
namespace App\Entity;

use App\People\Entity\StaffExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Staff
 */
class Staff extends StaffExtension
{
	/**
	 * @var string
	 */
	private $staffType;

	/**
	 * @var string
	 */
	private $jobTitle;

	/**
	 * @var string
	 */
	private $house;

	/**
	 * @var Collection
	 */
	private $departments;

	/**
	 * @var Space
	 */
	private $homeroom;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $profession;

	/**
	 * @var string
	 */
	private $employer;

	/**
	 * Staff constructor.
	 */
	public function __construct()
	{
		$this->departments = new ArrayCollection();
		parent::__construct();
	}

	/**
	 * Set staffType
	 *
	 * @param string $staffType
	 *
	 * @return Staff
	 */
	public function setStaffType($staffType)
	{
		$this->staffType = $staffType;

		return $this;
	}

	/**
	 * Get staffType
	 *
	 * @return string
	 */
	public function getStaffType()
	{
		if (empty($this->staffType))
			$this->setStaffType('Unknown');

		return $this->staffType;
	}

	/**
	 * Set jobTitle
	 *
	 * @param string $jobTitle
	 *
	 * @return Staff
	 */
	public function setJobTitle($jobTitle)
	{
		$this->jobTitle = $jobTitle;

		return $this;
	}

	/**
	 * Get jobTitle
	 *
	 * @return string
	 */
	public function getJobTitle()
	{
		if (empty($this->jobTitle))
			$this->setJobTitle('Not Specified');

		return $this->jobTitle;
	}

	/**
	 * Set house
	 *
	 * @param string $house
	 *
	 * @return Staff
	 */
	public function setHouse($house)
	{
		$this->house = $house;

		return $this;
	}

	/**
	 * Get house
	 *
	 * @return string
	 */
	public function getHouse()
	{
		return $this->house;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus(string $status): Staff
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getProfession(): string
	{
		return $this->profession;
	}

	/**
	 * @param string $profession
	 */
	public function setProfession(string $profession): Staff
	{
		$this->profession = $profession;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmployer(): string
	{
		return $this->employer;
	}

	/**
	 * @param string $employer
	 */
	public function setEmployer(string $employer): Staff
	{
		$this->employer = $employer;

		return $this;
	}

	/**
	 * @return null|Space
	 */
	public function getHomeroom(): ?Space
	{
		return $this->homeroom;
	}

	/**
	 * @param Space $homeroom
	 *
	 * @return Staff
	 */
	public function setHomeroom(Space $homeroom = null): Staff
	{
		$this->homeroom = $homeroom;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getDepartments(): Collection
	{
		return $this->departments;
	}

	/**
	 * @param Collection $departments
	 *
	 * @return Staff
	 */
	public function setDepartments(Collection $departments): Staff
	{
		$this->departments = $departments;

		return $this;
    }

	/**
	 * @param DepartmentMember $dept
	 *
	 * @return Staff
	 */
	public function removeDepartment(DepartmentMember $dept): Staff
	{
		if ($this->departments->contains($dept))
			$this->departments->removeElement($dept);

		return $this;
	}

	/**
	 * @param DepartmentMember $dept
	 *
	 * @return Staff
	 */
	public function addDepartment(DepartmentMember $dept): Staff
	{
		$dept->setStaff($this);

		if (!$this->departments->contains($dept))
			$this->departments->add($dept);

		return $this;
	}
}
