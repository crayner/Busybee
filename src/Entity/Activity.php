<?php
namespace App\Entity;

use App\School\Entity\ActivityExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

/**
 * Activity
 */
class Activity extends ActivityExtension
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
    private $nameShort;

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @var ArrayCollection
     */
    private $students;

    /**
     * @var bool
     */
    private $studentsSorted = false;

    /**
     * @var null|ArrayCollection
     */
    private $tutors;

    /**
     * @var Space
     */
    private $space;

    /**
     * @var Activity
     */
    private $studentReference;

    /**
     * @var integer
     */
    private $teachingLoad = 0;

    /**
     * @var boolean
     */
    private $reportable = 0;

    /**
     * @var boolean
     */
    private $attendance = 1;

    /**
     * @var null|string
     */
    private $website;

    /**
     * @var null|Collection
     */
    private $calendarGrades;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->setTeachingLoad(0);
        $this->studentsSorted = false;
        parent::__construct();
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
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return Activity
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = str_replace([' ', '\t', '\n', '\r', '\0', '\x0B'], '', strtoupper($nameShort));

        return $this;
    }

    /**
     * Get calendar
     *
     * @return \App\Entity\Calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Set calendar
     *
     * @param \App\Entity\Calendar $calendar
     *
     * @return Activity
     */
    public function setCalendar(\App\Entity\Calendar $calendar = null)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Add student
     *
     * @param Student $student
     *
     * @return Activity
     */
    public function addStudent(Student $student, $add = true): Activity
    {
        if ($add)
            $student->addActivity($this, false);

        if ($this->students->contains($student))
            return $this;

        $this->students->add($student);

        return $this;
    }

    /**
     * Remove student
     *
     * @param Student $student
     * @param bool $remove
     * @return Activity
     */
    public function removeStudent(Student $student, $remove = true): Activity
    {
        if ($remove)
            $student->removeActivity($this, false);

        $this->students->removeElement($student);

        return $this;
    }

    /**
     * Get students
     *
     * @return ArrayCollection
     */
    public function getStudents()
    {
        if ($this->getStudentReference() instanceof Activity)
            $this->students = $this->getStudentReference()->getStudents();

        if (!$this->studentsSorted && $this->students->count() > 0) {

            $iterator = $this->students->getIterator();
            $iterator->uasort(
                function ($a, $b) {
                    return ($a->formatName(['surnameFirst' => true]) < $b->formatName(['surnameFirst' => true])) ? -1 : 1;
                }
            );

            $this->students = new ArrayCollection(iterator_to_array($iterator, false));

            $this->studentsSorted = true;
        }

        return $this->students;
    }

    /**
     * Get studentReference
     *
     * @return \App\Entity\Activity
     */
    public function getStudentReference()
    {
        return $this->studentReference;
    }

    /**
     * Set studentReference
     *
     * @param \App\Entity\Activity $studentReference
     *
     * @return Activity
     */
    public function setStudentReference(\App\Entity\Activity $studentReference = null)
    {
        // stop self reference
        if ($studentReference instanceof Activity && $studentReference->getId() == $this->getId())
            $studentReference = null;

        $this->studentReference = $studentReference;

        return $this;
    }

    /**
     * Get ids
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get tutors
     *
     * @return null|ArrayCollection
     */
    public function getTutors(): ?Collection
    {
        $this->tutors = $this->tutors instanceof Collection ? $this->tutors : new ArrayCollection();

        return $this->tutors;
    }

    /**
     * Set tutors
     *
     * @param ArrayCollection|null $tutors
     *
     * @return Activity
     */
    public function setTutors(?ArrayCollection $tutors)
    {
        $this->tutors = $tutors;

        return $this;
    }

    /**
     * @param Staff|null $tutor
     * @return Activity
     */
    public function addTutor(?Staff $tutor): Activity
    {
        if (empty($tutor))
            return $this;

        if (!$this->getTutors()->contains($tutor))
            $this->tutors->add($tutor);

        return $this;
    }

    /**
     * @param Staff|null $tutor
     * @return Activity
     */
    public function removeTutor(?Staff $tutor): Activity
    {
        if (empty($tutor))
            return $this;

        $this->getTutors()->removeElement($tutor);

        return $this;
    }

    /**
     * Get space
     *
     * @return Space
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * Set space
     *
     * @param Space $space
     *
     * @return Activity
     */
    public function setSpace(Space $space = null)
    {
        $this->space = $space;

        return $this;
    }

    /**
     * Get teachingLoad
     *
     * @return integer
     */
    public function getTeachingLoad(): int
    {
        return intval($this->teachingLoad);
    }

    /**
     * Set teachingLoad
     *
     * @param integer $teachingLoad
     *
     * @return Activity
     */
    public function setTeachingLoad($teachingLoad): Activity
    {
        $this->teachingLoad = intval($teachingLoad);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGrades(): ?ArrayCollection
    {
        return $this->grades;
    }

    /**
     * @param ArrayCollection $grades
     * @return Activity
     */
    public function setGrades(?ArrayCollection $grades): Activity
    {
        $this->grades = $grades;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReportable(): bool
    {
        return $this->reportable ? true : false;
    }

    /**
     * @param bool $reportable
     * @return Activity
     */
    public function setReportable(?bool $reportable): Activity
    {
        $this->reportable = $reportable ? true : false;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAttendance(): bool
    {
        return $this->attendance ? true : false;
    }

    /**
     * @param bool $attendance
     * @return Activity
     */
    public function setAttendance(bool $attendance): Activity
    {
        $this->attendance = $attendance ? true : false;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param null|string $website
     * @return Activity
     */
    public function setWebsite(?string $website): Activity
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getCalendarGrades(): ?Collection
    {
        $this->calendarGrades = $this->calendarGrades ?: new ArrayCollection();

        if ($this->calendarGrades instanceof PersistentCollection)
            $this->calendarGrades->initialize();

        return $this->calendarGrades;
    }

    /**
     * @param Collection|null $calendarGrades
     * @return Activity
     */
    public function setCalendarGrades(?Collection $calendarGrades): Activity
    {
        $this->calendarGrades = $calendarGrades;
        return $this;
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @return Activity
     */
    public function addCalendarGrade(?CalendarGrade $calendarGrade): Activity
    {
        if (empty($calendarGrade) || $this->getCalendarGrades()->contains($calendarGrade))
            return $this;

        $this->calendarGrades->add($calendarGrade);

        return $this;
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @return Activity
     */
    public function removeCalendarGrade(?CalendarGrade $calendarGrade): Activity
    {
        if (empty($calendarGrade) || ! $this->getCalendarGrades()->contains($calendarGrade))
            return $this;

        $this->calendarGrades->removeElement($calendarGrade);

        return $this;
    }
}