<?php
namespace App\Entity;

use App\People\Entity\RollGroupExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

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
     * @var string!null
     */
	private $name;

    /**
     * @var string|null
     */
    private $nameShort;

    /**
     * @var string
     */
    private $website;

    /**
     * @var Calendar
     */
    private $calendar;

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

    /**
     * @var null|RollGroup
     */
    private $nextRoll;

    /**
     * @var null|string
     */
    private $grade;

    /**
     * @var null|Space
     */
    private $space;

    /**
     * RollGroup constructor.
     */
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
	 * Get calendar
	 *
	 * @return Calendar|null
	 */
	public function getCalendar(): ?Calendar
	{
		return $this->calendar;
	}

	/**
	 * Set calendar
	 *
	 * @param Calendar $calendar
	 *
	 * @return RollGroup
	 */
	public function setCalendar(Calendar $calendar = null, $add = true): RollGroup
	{
		$this->calendar = $calendar;

		if ($add)
		    $calendar->addRollGroup($this, false);

		return $this;
	}

    /**
     * @return Staff
     */
    public function getRollTutor1(): ?Staff
    {
        return $this->rollTutor1;
    }

    /**
     * @param Staff $rollTutor1
     * @return RollGroup
     */
    public function setRollTutor1(?Staff $rollTutor1): RollGroup
    {
        $this->rollTutor1 = $rollTutor1;
        return $this;
    }

    /**
     * @return Staff
     */
    public function getRollTutor2(): ?Staff
    {
        return $this->rollTutor2;
    }

    /**
     * @param Staff $rollTutor2
     * @return RollGroup
     */
    public function setRollTutor2(?Staff $rollTutor2): RollGroup
    {
        $this->rollTutor2 = $rollTutor2;
        return $this;
    }

    /**
     * @return Staff
     */
    public function getRollTutor3(): ?Staff
    {
        return $this->rollTutor3;
    }

    /**
     * @param Staff $rollTutor3
     * @return RollGroup
     */
    public function setRollTutor3(?Staff $rollTutor3): RollGroup
    {
        $this->rollTutor3 = $rollTutor3;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
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
    public function getNameShort(): ?string
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
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return RollGroup
     */
    public function setWebsite(string $website): RollGroup
    {
        $this->website = $website ?: null;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getStudents(): Collection
    {
        if ($this->students instanceof PersistentCollection)
            $this->students->initialize();

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
    public function addStudent(?Student $student, $addStudent = true): RollGroup
    {
        $this->getStudents();

        if (is_null($student))
            return $this;

        if ($addStudent)
            $student->addRollGroup($this, false);

        if (! $this->students->contains($student))
            $this->students->add($student);

        return $this;
    }

    /**
     * @param Student $student
     * @return RollGroup
     */
    public function removeStudent(Student $student): RollGroup
    {
        if ($this->students->contains($student))
            $this->students->removeElement($student);

        return $this;
    }

    /**
     * @return null|RollGroup
     */
    public function getNextRoll(): ?RollGroup
    {
        return $this->nextRoll;
    }

    /**
     * @param RollGroup $nextRoll
     * @return RollGroup
     */
    public function setNextRoll(RollGroup $nextRoll): RollGroup
    {
        $this->nextRoll = $nextRoll ?: null;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getGrade(): ?string
    {
        return $this->grade;
    }

    /**
     * @param null|string $grade
     * @return RollGroup
     */
    public function setGrade(?string $grade): RollGroup
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * @return Space|null
     */
    public function getSpace(): ?Space
    {
        return $this->space;
    }

    /**
     * @param Space|null $space
     * @return RollGroup
     */
    public function setSpace(?Space $space): RollGroup
    {
        $this->space = $space;
        return $this;
    }
}
