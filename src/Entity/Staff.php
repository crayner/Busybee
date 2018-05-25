<?php
namespace App\Entity;

use App\People\Entity\StaffExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

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
	public function setHouse($house): Staff
	{
		$this->house = strtolower($house);
		return $this;
	}

	/**
	 * Get house
	 *
	 * @return string
	 */
	public function getHouse(): ?string
	{
		return strtolower($this->house);
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

    /**
     * @var null|Collection
     */
    private $commitments;

    /**
     * @return Collection
     */
    public function getCommitments(): Collection
    {
        $this->commitments = $this->commitments instanceof Collection ? $this->commitments : new ArrayCollection();

        if ($this->commitments instanceof PersistentCollection && ! $this->commitments->isInitialized())
            $this->commitments->initialize();

        return $this->commitments;
    }

    /**
     * @param ArrayCollection|null $commitments
     * @return Student
     */
    public function setCommitments(?ArrayCollection $commitments): Person
    {
        if (empty($commitments))
            return $this;

        $this->commitments = $commitments;

        return $this;
    }

    /**
     * @param ActivityTutor|null $activityTutor
     * @param bool $add
     * @return Person
     */
    public function addCommitment(?ActivityTutor $activityTutor, $add = true): Person
    {
        if (empty($activityTutor))
            return $this;

        if ($add)
            $activityTutor->setTutor($this, false);

        if ($this->getCommitments()->contains($activityTutor))
            return $this;

        $this->commitments->add($activityTutor);

        return $this;
    }

    /**
     * @param ActivityTutor $activityTutor
     * @return Person
     */
    public function removeCommitment(ActivityTutor $activityTutor): Person
    {
        $this->getCommitments()->removeElement($activityTutor);

        return $this;
    }


    /**
     * @var Collection
     */
    private $periodCommitments;

    /**
     * @return Collection
     */
    public function getPeriodCommitments(): Collection
    {
        if (empty($this->periodCommitments))
            $this->periodCommitments = new ArrayCollection();

        if ($this->periodCommitments instanceof PersistentCollection && ! $this->periodCommitments->isInitialized())
            $this->periodCommitments->initialize();

        return $this->periodCommitments;
    }

    /**
     * @param null|Collection $periodCommitments
     * @return Staff
     */
    public function setPeriodCommitments(?Collection $periodCommitments): Staff
    {
        $this->periodCommitments = $periodCommitments;
        return $this;
    }

    /**
     * @param TimetablePeriodActivityTutor|null $activity
     * @param bool $add
     * @return Staff
     */
    public function addPeriodCommitment(?TimetablePeriodActivityTutor $activity, $add = true): Staff
    {
        if ($activity && $this->getPeriodCommitments()->contains($activity))
            return $this;

        if ($add)
            $activity->setTutor($this, false);

        $this->periodCommitments->add($activity);

        return $this;
    }

    /**
     * @param TimetablePeriodActivityTutor|null $activity
     * @return Staff
     */
    public function removePeriodCommitment(?TimetablePeriodActivityTutor $activity): Staff
    {
        $this->getPeriodCommitments()->removeElement($activity);
        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
