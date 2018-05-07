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
    private $code;

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
     * @var null|Collection
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Activity
     */
    public function setCode($code)
    {
        $this->code = str_replace([' ', '\t', '\n', '\r', '\0', '\x0B'], '', strtoupper($code));

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
     * @param ActivityStudent $student
     *
     * @return Activity
     */
    public function addStudent(?ActivityStudent $student, $add = true): Activity
    {
        if (empty($student) || $this->getStudents()->contains($student))
            return $this;

        if ($add)
            $student->setActivity($this, false);

        $this->students->add($student);

        return $this;
    }

    /**
     * Remove student
     *
     * @param ActivityStudent $student
     * @param bool $remove
     * @return Activity
     */
    public function removeStudent(ActivityStudent $student, $remove = true): Activity
    {
        if ($remove)
            $student->setActivity(null, false);

        $this->students->removeElement($student);

        return $this;
    }

    /**
     * Get students
     *
     * @return Collection
     */
    public function getStudents($sort = false): Collection
    {
        if (empty($this->students))
            $this->students = new ArrayCollection();

        if ($this->students instanceof PersistentCollection && ! $this->students->isInitialized())
            $this->students->initialize();

        if ($this->getStudentReference() instanceof Activity)
            $this->students = $this->getStudentReference()->getStudents();

        if ($sort)
            $this->studentsSorted = false;

        if (! $this->studentsSorted && $this->students->count() > 0) {

            $iterator = $this->students->getIterator();
            $iterator->uasort(
                function ($a, $b) {
                    return ($a->getStudent()->formatName(['surnameFirst' => true]) < $b->getStudent()->formatName(['surnameFirst' => true])) ? -1 : 1;
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
     * @param Activity $studentReference
     *
     * @return Activity
     */
    public function setStudentReference(Activity $studentReference = null)
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
     * @return Collection
     */
    public function getTutors(): Collection
    {
        $this->tutors = $this->tutors instanceof Collection ? $this->tutors : new ArrayCollection();

        if ($this->tutors instanceof PersistentCollection && ! $this->tutors->isInitialized())
            $this->tutors->initialize();

        return $this->tutors;
    }

    /**
     * Set tutors
     *
     * @param Collection|null $tutors
     *
     * @return Activity
     */
    public function setTutors(?Collection $tutors)
    {
        $this->tutors = $tutors;

        return $this;
    }

    /**
     * @param ActivityTutor|null $tutor
     * @return Activity
     */
    public function addTutor(?ActivityTutor $tutor, $add = true): Activity
    {
        if (empty($tutor) || $this->getTutors()->contains($tutor))
            return $this;

        if ($add)
            $tutor->setActivity($this, false);

        $this->tutors->add($tutor);

        return $this;
    }

    /**
     * @param ActivityTutor|null $tutor
     * @return Activity
     */
    public function removeTutor(?ActivityTutor $tutor): Activity
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
     * @return Collection
     */
    public function getCalendarGrades(): Collection
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

    /**
     * @var null|Course
     */
    private $course;

    /**
     * @return Course|null
     */
    public function getCourse(): ?Course
    {
        if ($this->course instanceof Course)
            $this->course->getId();
        return $this->course;
    }

    /**
     * @param Course|null $courses
     * @return FaceToFace
     */
    public function setCourse(?Course $course, $add = true): FaceToFace
    {
        if (empty($course))
            $course = null;

        if ($add)
            $course->addActivity($this, false);

        $this->course = $course;

        return $this;
    }

    /**
     * @param null|Collection $students
     * @return Activity
     */
    public function setStudents(?Collection $students): Activity
    {
        $this->students = $students;

        return $this;
    }

    /**
     * @var bool
     */
    private $useCourseName;

    /**
     * @return bool
     */
    public function isUseCourseName(): bool
    {
        return $this->useCourseName ? true : false ;
    }

    /**
     * @param bool $useCourseName
     * @return Activity
     */
    public function setUseCourseName(bool $useCourseName): Activity
    {
        $this->useCourseName = $useCourseName ? true : false ;
        return $this;
    }

}