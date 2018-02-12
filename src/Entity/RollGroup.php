<?php
namespace App\Entity;

use App\People\Entity\RollGroupExtension;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Student Calendar Group
 */
class RollGroup extends RollGroupExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $status;

    /**
     * @var string
     */
	private $name;

    /**
     * @var string
     */
    private $nameShort;

    /**
     * @var string
     */
    private $website;

    /**
     * @var CalendarGroup
     */
    private $calendarGroup;

    /**
     * @var Staff
     */
    private $rollTutor1;

    /**
     * @var Staff
     */
    private $rollTutor2;

    /**
     * @var Staff
     */
    private $rollTutor3;

    /**
     * @var ArrayCollection
     */
    private $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
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
	 * Get status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set status
	 *
	 * @param string $status
	 *
	 * @return RollGroup
	 */
	public function setStatus($status): RollGroup
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * Get calendarGroup
	 *
	 * @return CalendarGroup
	 */
	public function getCalendarGroup()
	{
		return $this->calendarGroup;
	}

	/**
	 * Set calendarGroup
	 *
	 * @param CalendarGroup $calendarGroup
	 *
	 * @return RollGroup
	 */
	public function setCalendarGroup(CalendarGroup $calendarGroup = null, $add = true): RollGroup
	{
		if ($add)
			$calendarGroup->addStudent($this, false);

		$this->calendarGroup = $calendarGroup;

		return $this;
	}

    /**
     * @return Staff
     */
    public function getRollTutor1(): Staff
    {
        return $this->rollTutor1;
    }

    /**
     * @param Staff $rollTutor1
     * @return RollGroup
     */
    public function setRollTutor1(Staff $rollTutor1): RollGroup
    {
        $this->rollTutor1 = $rollTutor1;
        return $this;
    }

    /**
     * @return Staff
     */
    public function getRollTutor2(): Staff
    {
        return $this->rollTutor2;
    }

    /**
     * @param Staff $rollTutor2
     * @return RollGroup
     */
    public function setRollTutor2(Staff $rollTutor2): RollGroup
    {
        $this->rollTutor2 = $rollTutor2;
        return $this;
    }

    /**
     * @return Staff
     */
    public function getRollTutor3(): Staff
    {
        return $this->rollTutor3;
    }

    /**
     * @param Staff $rollTutor3
     * @return RollGroup
     */
    public function setRollTutor3(Staff $rollTutor3): RollGroup
    {
        $this->rollTutor3 = $rollTutor3;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RollGroup
     */
    public function setName(string $name): RollGroup
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameShort(): string
    {
        return $this->nameShort;
    }

    /**
     * @param string $nameShort
     * @return RollGroup
     */
    public function setNameShort(string $nameShort): RollGroup
    {
        $this->nameShort = $nameShort;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return RollGroup
     */
    public function setWebsite(string $website): RollGroup
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStudents(): ArrayCollection
    {
        return $this->students;
    }

    /**
     * @param ArrayCollection $students
     * @return RollGroup
     */
    public function setStudents(ArrayCollection $students): RollGroup
    {
        $this->students = $students;
        return $this;
    }

    /**
     * @param Student $student
     * @param bool $addStudent
     * @return Student
     */
    public function addStudent(Student $student, $addStudent = true): Student
    {
        if ($addStudent)
            $student->addRoll($this, false);

        if (! $this->students->contains($student))
            $this->students->add($student);

        return $this;
    }

    /**
     * @param Student $student
     * @return Student
     */
    public function removeStudent(Student $student): Student
    {
        if ($this->students->contains($student))
            $this->students->removeElement($student);

        return $this;
    }
}
