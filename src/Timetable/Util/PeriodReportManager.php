<?php
namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\FaceToFace;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\Student;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PeriodReportManager extends ReportManager
{
    /**
     * PeriodReportManager constructor.
     * @param TimetablePeriod $period
     */
    public function __construct(TimetablePeriod $period)
    {
        $this->setPeriod($period);
    }

    /**
     * @var array|null
     */
    private $grades;

    /**
     * @return array|null
     */
    public function getGrades(): ?array
    {
        return $this->grades;
    }

    /**
     * @param array|null $grades
     * @return PeriodReportManager
     */
    public function setGrades(?array $grades): PeriodReportManager
    {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @param $id
     * @return CalendarGrade
     */
    public function getGrade($id): CalendarGrade
    {
        $grades = $this->getGrades();
        if (isset($grades[$id]) && $grades[$id] instanceof CalendarGrade)
            return $grades[$id];

        $grade = new CalendarGrade();
        $grade->setGrade('xx');
        $grade->setCalendar($this->getCurrentCalendar());
        return $grade;
    }

    /**
     * @var Collection|null
     */
    private $possibleStudents;

    /**
     * @var int
     */
    private $possibleStudentCount = 0;

    /**
     * @return Collection|null
     */
    public function getPossibleStudents(): Collection
    {
        if (empty($this->possibleStudents))
            $this->possibleStudents = new ArrayCollection();

        return $this->possibleStudents;
    }

    /**
     * @param Collection $students
     * @return PeriodReportManager
     */
    public function addPossibleStudentGrade(?CalendarGrade $grade): PeriodReportManager
    {
        if (empty($grade))
            return $this;

        $students = new ArrayCollection();
        foreach($grade->getStudents() as $student)
            $students->add($student->getStudent());

        $this->getPossibleStudents()->set($grade->getId(), $students);
        $this->possibleStudentCount += $grade->getStudents()->count();
        return $this;

    }

    /**
     * @return int
     */
    public function getPossibleStudentCount(): int
    {
        return $this->possibleStudentCount;
    }

    /**
     * @var Collection|null
     */
    private $missingStudents;

    /**
     * @var int
     */
    private $missingStudentCount = 0;

    /**
     * @return Collection|null
     */
    public function getMissingStudents(): Collection
    {
        if (empty($this->missingStudents))
            $this->missingStudents = new ArrayCollection();

        if ($this->getPossibleStudentCount() === 0)
            return $this->missingStudents;

        foreach($this->getPossibleStudents()->getIterator() as $id=>$studentList)
        {
            $grade = isset($this->getGrades()[$id]) ? $this->getGrades()[$id] : null;
            $students = $this->getAllocatedStudentGrade($grade);
            foreach($studentList->getIterator() as $student)
            {
                if (!$students->contains($student))
                    $this->addMissingStudent($student, $grade);
            }
        }
        return $this->missingStudents;
    }

    /**
     * @return int
     */
    public function getMissingStudentCount(): int
    {
        return $this->missingStudentCount;
    }

    /**
     * @param Student $student
     * @param CalendarGrade|null $grade
     * @return PeriodReportManager
     */
    public function addMissingStudent(Student $student, ?CalendarGrade $grade): PeriodReportManager
    {
        if (empty($student))
            return $this;

        $students = $this->getMissingStudentGrade($grade);

        if ($students->contains($student))
            return $this;

        $students->add($student);
        $this->missingStudentCount++;

        $this->missingStudents->set($this->getGradeID($grade), $students);

        return $this;
    }

    /**
     * @param CalendarGrade|null $grade
     * @return Collection
     */
    public function getMissingStudentGrade(?CalendarGrade $grade): Collection
    {
        if (empty($this->missingStudents))
            $this->missingStudents = new ArrayCollection();
        $id = $this->getGradeID($grade);
        if ($this->missingStudents->containsKey($id))
            $students = $this->missingStudents->get($id);
        if (empty($students))
            $students = new ArrayCollection();

        return $students;
    }

    /**
     * @var Collection|null
     */
    private $allocatedStudents;

    /**
     * @var int
     */
    private $allocatedStudentCount = 0;

    /**
     * @return Collection|null
     */
    public function getAllocatedStudents(): ?Collection
    {
        if (empty($this->allocatedStudents))
            $this->allocatedStudents = new ArrayCollection();

        return $this->allocatedStudents;
    }

    /**
     * @return int
     */
    public function getAllocatedStudentCount(): int
    {
        return $this->allocatedStudentCount;
    }

    /**
     * @param FaceToFace|null $act
     * @return PeriodReportManager
     */
    public function addAllocatedStudents(?FaceToFace $act): PeriodReportManager
    {
        if (empty($act))
            return $this;
        foreach ($act->getStudents() as $student) {
            $grade = $student->getStudent()->getStudentCurrentGrade($this->getCurrentCalendar());
            $id = $this->getGradeID($grade);
            $students = $this->getAllocatedStudentGrade($grade);
            if ($students->contains($student->getStudent()))
                $this->addDuplicateStudent($student, $grade);
            else {
                $students->add($student->getStudent());
                $this->allocatedStudentCount++;
            }
            $this->setAllocatedStudentGrade($students, $grade);
        }

        return $this;
    }

    /**
     * @param CalendarGrade|null $grade
     * @return Collection
     */
    public function getAllocatedStudentGrade(?CalendarGrade $grade): Collection
    {
        $id = $this->getGradeID($grade);
        if ($this->getAllocatedStudents()->containsKey($id))
            $students = $this->getAllocatedStudents()->get($id);
        if (empty($students))
            $students = new ArrayCollection();

        return $students;
    }

    /**
     * @param Collection $students
     * @param CalendarGrade|null $grade
     * @return PeriodReportManager
     */
    public function setAllocatedStudentGrade(Collection $students, ?CalendarGrade $grade): PeriodReportManager
    {
        $id = $this->getGradeID($grade);
        $this->getAllocatedStudents()->set($id, $students);
        return $this;
    }

    /**
     * @var Collection|null
     */
    private $duplicateStudents;

    /**
     * @var int
     */
    private $duplicateStudentCount = 0;

    /**
     * @return Collection
     */
    public function getDuplicateStudents(): Collection
    {
        if (empty($this->duplicateStudents))
            $this->duplicateStudents = new ArrayCollection();
        return $this->duplicateStudents;
    }

    /**
     * @param Student $student
     * @param CalendarGrade|null $grade
     * @return PeriodReportManager
     */
    public function addDuplicateStudent(Student $student, ?CalendarGrade $grade): PeriodReportManager
    {
        if (empty($student))
            return $this;

        $students = $this->getDuplicateStudentGrade($grade);
        if ($students->contains($student))
            return $this;

        $students->add($student);
        $this->setDuplicateStudentGrade($students, $grade);
        $this->duplicateStudentCount++;

        return $this;
    }

    /**
     * @param CalendarGrade|null $grade
     * @return Collection
     */
    public function getDuplicateStudentGrade(?CalendarGrade $grade): Collection
    {
        $id = $this->getGradeID($grade);
        if ($this->getDuplicateStudents()->containsKey($id))
            $students = $this->getDuplicateStudents()->get($id);
        if (empty($students))
            $students = new ArrayCollection();

        return $students;
    }

    /**
     * @param Collection $students
     * @param CalendarGrade|null $grade
     * @return PeriodReportManager
     */
    public function setDuplicateStudentGrade(Collection $students, ?CalendarGrade $grade): PeriodReportManager
    {
        $id = $this->getGradeID($grade);
        $this->getDuplicateStudents()->set($id, $students);
        return $this;
    }

    /**
     * @param CalendarGrade|null $grade
     * @return int
     */
    private function getGradeID(?CalendarGrade $grade): int
    {
        if (empty($grade))
            return 0;
        return intval($grade->getId());
    }

    /**
     * @var Calendar|null
     */
    private $currentCalendar;

    /**
     * @return Calendar|null
     */
    public function getCurrentCalendar(): ?Calendar
    {
        return $this->currentCalendar;
    }

    /**
     * @param Calendar|null $currentCalendar
     * @return PeriodReportManager
     */
    public function setCurrentCalendar(?Calendar $currentCalendar): PeriodReportManager
    {
        $this->currentCalendar = $currentCalendar;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $possibleSpaces;

    /**
     * @return Collection
     */
    public function getPossibleSpaces(): Collection
    {
        if (empty($this->possibleSpaces))
            $this->possibleSpaces = new ArrayCollection();
        return $this->possibleSpaces;
    }

    /**
     * @param Collection|null $possibleSpaces
     * @return PeriodReportManager
     */
    public function setPossibleSpaces(?Collection $possibleSpaces): PeriodReportManager
    {
        $this->possibleSpaces = $possibleSpaces;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $allocatedSpaces;

    /**
     * @return Collection|null
     */
    public function getAllocatedSpaces(): Collection
    {
        if (empty($this->allocatedSpaces))
            $this->allocatedSpaces = new ArrayCollection();
        return $this->allocatedSpaces;
    }

    /**
     * @return PeriodReportManager
     */
    public function setAllocatedSpaces(): PeriodReportManager
    {
        if (empty($this->getPeriod()) || empty($this->getPeriod()->getActivities()))
            return $this;

        foreach ($this->getPeriod()->getActivities()->getIterator() as $activity)
            if ($activity->loadSpace())
                if (!$this->getAllocatedSpaces()->contains($activity->loadSpace()))
                    $this->allocatedSpaces->add($activity->loadSpace());
                else
                    $this->addDuplicateSpace($activity->loadSpace());

        return $this;
    }

    /**
     * @var Collection|null
     */
    private $duplicateSpaces;

    /**
     * @return Collection
     */
    public function getDuplicateSpaces(): Collection
    {
        if (empty($this->duplicateSpaces))
            $this->duplicateSpaces = new ArrayCollection();
        return $this->duplicateSpaces;
    }

    /**
     * @param Space|null $space
     * @return PeriodReportManager
     */
    public function addDuplicateSpace(?Space $space): PeriodReportManager
    {
        if (empty($space) || $this->getDuplicateSpaces()->contains($space))
            return $this;

        $this->duplicateSpaces->add($space);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAvailableSpaces(): Collection
    {
        $diff = array_diff($this->getPossibleSpaces()->toArray(), $this->getAllocatedSpaces()->toArray());
        return new ArrayCollection($diff);
    }


    /**
     * @var null|Collection
     */
    private $possibleTutors;

    /**
     * @return Collection
     */
    public function getPossibleTutors(): Collection
    {
        if (empty($this->possibleTutors))
            $this->possibleTutors = new ArrayCollection();
        return $this->possibleTutors;
    }

    /**
     * @param Collection|null $possibleTutors
     * @return PeriodReportManager
     */
    public function setPossibleTutors(?Collection $possibleTutors): PeriodReportManager
    {
        $this->possibleTutors = $possibleTutors;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $allocatedTutors;

    /**
     * @var int
     */
    private $allocatedTutorCount = 0;

    /**
     * @return Collection
     */
    public function getAllocatedTutors(): Collection
    {
        if (empty($this->allocatedTutors))
            $this->allocatedTutors = new ArrayCollection();
        return $this->allocatedTutors;
    }

    /**
     * @return PeriodReportManager
     */
    public function setAllocatedTutors(): PeriodReportManager
    {
        if (empty($this->getPeriod()) || empty($this->getPeriod()->getActivities()))
            return $this;

        foreach ($this->getPeriod()->getActivities()->getIterator() as $activity)
            if ($activity->loadTutors())
            {
                foreach($activity->loadTutors()->getIterator() as $at)
                {
                    $this->addAllocatedTutor($at->getTutor());
                }
            }
        return $this;
    }

    /**
     * @param Staff|null $tutor
     * @return PeriodReportManager
     */
    public function addAllocatedTutor(?Staff $tutor): PeriodReportManager
    {
        if (empty($tutor))
            return $this;

        if ($this->getAllocatedTutors()->contains($tutor)) {
            $this->addDuplicateTutor($tutor);
            return $this;
        }

        $this->allocatedTutors->add($tutor);
        $this->allocatedTutorCount++;

        return $this;
    }

    /**
     * @var Collection|null
     */
    private $duplicateTutors;

    /**
     * @var integer
     */
    private $duplicateTutorCount = 0;

    /**
     * @return Collection|null
     */
    public function getDuplicateTutors(): ?Collection
    {
        if (empty($this->duplicateTutors))
            $this->duplicateTutors = new ArrayCollection();
        return $this->duplicateTutors;
    }

    /**
     * @param Staff|null $tutor
     * @return PeriodReportManager
     */
    public function addDuplicateTutor(?Staff $tutor): PeriodReportManager
    {
        if (empty($staff) || $this->getDuplicateTutors()->contains($tutor))
            return $this;

        $this->getDuplicateTutors()->add($tutor);
        $this->duplicateTutorCount++;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAvailableTutors(): Collection
    {
        $diff = array_diff($this->getPossibleTutors()->toArray(), $this->getAllocatedTutors()->toArray());
        return new ArrayCollection($diff);
    }

    /**
     * @var TimetablePeriod
     */
    private $period;

    /**
     * @return TimetablePeriod
     */
    public function getPeriod(): TimetablePeriod
    {
        return $this->period;
    }

    /**
     * @param TimetablePeriod $period
     * @return PeriodReportManager
     */
    public function setPeriod(TimetablePeriod $period): PeriodReportManager
    {
        $this->period = $period;
        return $this;
    }

    /**
     * @var Collection|null
     */
    private $activityReports;

    /**
     * @return Collection|null
     */
    public function getActivityReports(): ?Collection
    {
        if (empty($this->activityReports))
            $this->activityReports = new ArrayCollection();
        return $this->activityReports;
    }

    /**
     * @param Collection|null $activityReports
     * @return PeriodReportManager
     */
    public function setActivityReports(): PeriodReportManager
    {
        if (empty($this->getGrades()))
            return $this;

        foreach($this->getPeriod()->getActivities()->getIterator() As $activity) {
            // test for grade
            dump([$this->getGrades(), $activity]);
            foreach($activity->getActivity->getCalendarGrades()->getIterator() as $grade)
            {
                if (in_array($grade, $this->getGrades())) {
                    $this->addActivityReport($activity);
                    break;
                }

            }

        }
        return $this;
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return PeriodReportManager
     */
    public function addActivityReport(?TimetablePeriodActivity $activity): PeriodReportManager
    {
        if (empty($activity))
            return $this;
        dump($activity);
        $this->getActivityReports()->add(new ActivityReportManager($activity));
        
        return $this;
    }

    /**
     * @var bool
     */
    private $disableDrop = false;

    /**
     * @return bool
     */
    public function isDisableDrop(): bool
    {
        return $this->disableDrop;
    }

    /**
     *
     */
    public function getActivityReportsStatus(array $grades): PeriodReportManager
    {
        foreach($this->getActivityReports()->getIterator() as $report)
            $report->getActivityStatus();
        return $this;
    }
}