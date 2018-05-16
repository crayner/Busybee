<?php
namespace App\Entity;

use App\School\Entity\CourseExtension;
use App\School\Form\FaceToFaceType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

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
     * @var null|boolean
     */
    private $map;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var null|Department
     */
    private $department;

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
	public function setName(?string $name): Course
	{
		$this->name = $name;

		return $this;
	}

    /**
     * @return bool
     */
    public function getMap(): bool
    {
        return $this->map ? true : false ;
    }

    /**
     * @param bool|null $map
     * @return Course
     */
    public function setMap(?bool $map): Course
    {
        $this->map = $map ? true : false ;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return Course
     */
    public function setDescription(?string $description): Course
    {
        $this->description = $description ?: null ;

        return $this;
    }

    /**
     * @return Department|null
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * @param Department|null $department
     * @return Course
     */
    public function setDepartment(?Department $department, $add = true): Course
    {
        if ($add && ! empty($department))
            $department->addCourse($this, false);

        $this->department = $department;

        return $this;
    }

    /**
     * @var null|Collection
     */
    private $calendarGrades;

    /**
     * @return Collection|null
     */
    public function getCalendarGrades(): Collection
    {
        if (empty($this->calendarGrades))
            $this->calendarGrades = new ArrayCollection();

        if ($this->calendarGrades instanceof PersistentCollection && ! $this->calendarGrades->isInitialized())
            $this->calendarGrades->initialize();

        return $this->calendarGrades;
    }

    /**
     * @param Collection|null $calendarGrades
     * @return Course
     */
    public function setCalendarGrades(?Collection $calendarGrades): Course
    {
        if (empty($calendarGrades))
            return $this;

        $this->calendarGrades = $calendarGrades;

        return $this;
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @return Course
     */
    public function addCalendarGrade(?CalendarGrade $calendarGrade): Course
    {
        if (empty($calendarGrade) || $this->getCalendarGrades()->contains($calendarGrade))
            return $this;

        $this->calendarGrades->add($calendarGrade);

        return $this;
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @return Course
     */
    public function removeCalendarGrade(?CalendarGrade $calendarGrade): Course
    {
        $this->getCalendarGrades()->removeElement($calendarGrade);

        return $this;
    }

    /**
     * @var int
     */
    private $sortBy = 0;

    /**
     * @return int
     */
    public function getSortBy(): int
    {
        return $this->sortBy;
    }

    /**
     * @param int $sortBy
     * @return Course
     */
    public function setSortBy(int $sortBy): Course
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * @var null|Collection
     */
    private $activities;

    /**
     * @return Collection|null
     */
    public function getActivities(): ?Collection
    {
        if (empty($this->activities))
            $this->activities = new ArrayCollection();

        if ($this->activities instanceof PersistentCollection && ! $this->activities->isInitialized()) {
            $this->activities->initialize();

            $iterator = $this->activities->getIterator();

            $iterator->uasort(
                function ($a, $b) {
                    return ($a->getFullName() < $b->getFullName()) ? -1 : 1;
                }
            );

            $this->students = new ArrayCollection(iterator_to_array($iterator, false));
        }

        return $this->activities;
    }

    /**
     * @param Collection|null $activities
     * @return Course
     */
    public function setActivities(?Collection $activities): Course
    {
        $this->activities = $activities;

        return $this;
    }

    /**
     * @param Activity $activity
     * @param bool $add
     * @return Course
     */
    public function addActivity(Activity $activity, $add = true): Course
    {
        if (empty($activity) || ! $activity instanceof FaceToFace)
            return $this;

        if ($add)
            $activity->setCourse($this, false);

        if ($this-->$this->getActivities()->contains($activity))
            return $this;

        $this->activities->add($activity);

        return $this;
    }

    /**
     * @param FaceToFace $activity
     * @return Course
     */
    public function removeActivity(FaceToFace $activity): Course
    {
        if (empty($activity))
            return $this;

        $activity->setCourse(null, false);

        $this->activities->removeElement($activity);

        return $this;
    }

    /**
     * @param int $id
     * @return Course
     */
    public function setId(int $id): Course
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var null|TimetableLine
     */
    private $line;

    /**
     * @return TimetableLine|null
     */
    public function getLine(): ?TimetableLine
    {
        return $this->line;
    }

    /**
     * @param TimetableLine|null $line
     * @return Course
     */
    public function setLine(?TimetableLine $line, $add = true): Course
    {
        if (empty($line)) {
            $this->line = null;
            return $this;
        }

        if ($add)
            $line->addCourse($this, false);

        $this->line = $line;

        return $this;
    }
}
