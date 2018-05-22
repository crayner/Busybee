<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Department Member
 */
class DepartmentMember
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $staffType;

	/**
	 * @var Department
	 */
	private $department;

	/**
	 * @var ArrayCollection
	 */
	private $staff;

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
     * @param int $id
     * @return DepartmentMember
     */
    public function setId(?int $id): DepartmentMember
    {
        $this->id = $id;

        return $this;
    }

	/**
	 * Get staffType
	 *
	 * @return string
	 */
	public function getStaffType()
	{
		return $this->staffType;
	}

	/**
	 * Set staffType
	 *
	 * @param string $staffType
	 *
	 * @return DepartmentMember
	 */
	public function setStaffType($staffType)
	{
		$this->staffType = $staffType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		if (is_null($this->getDepartment()))
			return $this->getStaff()->formatName();

		return $this->getStaff()->formatName() . ' in department ' . $this->getDepartment()->getName();
	}

	/**
	 * Get staff
	 *
	 * @return Staff|null
	 */
	public function getStaff(): ?Staff
	{
		return $this->staff;
	}

	/**
	 * Set staff
	 *
	 * @param Staff $staff
	 *
	 * @return DepartmentMember
	 */
	public function setStaff(Staff $staff): DepartmentMember
	{
		$this->staff = $staff;

		return $this;
	}

	/**
	 * @return Department
	 */
	public function getDepartment(): ?Department
	{
		return $this->department;
	}

	/**
	 * @param Department $department
	 *
	 * @return DepartmentMember
	 */
	public function setDepartment(?Department $department, $add = true): DepartmentMember
	{
		$this->department = $department;

		if ($add && ! empty($department))
		    $department->addMember($this, false);

		return $this;
	}

	public function getFullStaffName(): ?string
    {
        if ($this->getStaff())
            return $this->getStaff()->getFullName();

        return '';
    }
}
