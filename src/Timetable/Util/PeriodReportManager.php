<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 9/05/2018
 * Time: 16:15
 */

namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Util\ReportManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\Space;
use App\Entity\Student;
use App\Entity\TimetablePeriodActivity;
use App\Timetable\Organism\SpaceActivity;
use App\Timetable\Organism\TutorActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PeriodReportManager extends ReportManager
{

    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * getGrades
     *
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        if (empty($this->grades))
            throw new \InvalidArgumentException('The grades need to be injected into the report.');
        return $this->grades;
    }

    /**
     * setGrades
     *
     * @param ArrayCollection $grades
     * @return PeriodReportManager
     */
    public function setGrades(ArrayCollection $grades): PeriodReportManager
    {
        if ($this->grades !== $grades)
            $this->setRefreshReport(true);
        $this->grades = $grades;
        return $this;
    }

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * getCalendar
     *
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        if (empty($this->calendar))
            throw new \InvalidArgumentException('Injection of the current calendar is required.');
        return $this->calendar;
    }

    /**
     * setCalendar
     *
     * @param Calendar $calendar
     * @return PeriodReportManager
     */
    public function setCalendar(Calendar $calendar): PeriodReportManager
    {
        $this->calendar = $calendar;
        return $this;
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
     * getPossibleStudents
     *
     * @return Collection
     */
    public function getPossibleStudents(): Collection
    {
        if (empty($this->possibleStudents))
            $this->possibleStudents = new ArrayCollection();

        return $this->possibleStudents;
    }

    /**
     * addPossibleStudents
     *
     * @return PeriodReportManager
     */
    public function addPossibleStudents(): PeriodReportManager
    {
        foreach($this->getGrades()->getIterator() as $grade)
            $this->addPossibleStudentGrade($grade);
        return $this;
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
     * getPossibleStudentCount
     *
     * @return int
     */
    public function getPossibleStudentCount(): int
    {
        return $this->possibleStudentCount;
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
     * @return PeriodReportManager
     */
    public function addAllocatedStudents(): PeriodReportManager
    {
        foreach ($this->getActivities() as $activity) {
            $newStudents = $activity->getAllocatedStudents();
            if ($newStudents->count() === 0)
                continue;
            foreach($newStudents as $as) {
                $student = $as->getStudent();
                $currentStudents = $this->getAllocatedStudentGrade($student->getGradeInCurrentGrade());
                if ($currentStudents->contains($student))
                    $this->addDuplicateStudent($student, $student->getGradeInCurrentGrade());
                else {
                    $currentStudents->add($student);
                    $this->setAllocatedStudentGrade($currentStudents, $student->getGradeInCurrentGrade());
                    $this->allocatedStudentCount++;
                }
            }
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
     * setAllocatedStudentGrade
     *
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
     * @var ArrayCollection
     */
    private $activities;

    /**
     * getActivities
     *
     * @return ArrayCollection
     */
    public function getActivities(): ArrayCollection
    {
        if (empty($this->activities))
            $this->activities = new ArrayCollection();
        return $this->activities;
    }

    /**
     * addActivity
     *
     * @param TimetablePeriodActivity $activity
     * @return PeriodReportManager
     */
    public function addActivity(TimetablePeriodActivity $activity): PeriodReportManager
    {
        $report = new ActivityReportManager();
        $report = $report->setEntityManager($this->getEntityManager())->retrieveCache($activity, TimetablePeriodActivity::class);

        $report->setGrades($this->getGrades());
        $this->getActivities()->set($activity->getId(), $report);
        return $this;
    }

    /**
     * generateActivityReports
     *
     * @return PeriodReportManager
     */
    public function generateActivityReports(): PeriodReportManager
    {
        foreach($this->getEntity()->getActivities()->getIterator() as $activity)
            $this->addActivity($activity);
        return $this;
    }

    /**
     * @var bool
     */
    private $disableDrop = false;

    /**
     * isDisableDrop
     *
     * @return bool
     */
    public function isDisableDrop(): bool
    {
        return $this->disableDrop;
    }

    /**
     * setDisableDrop
     *
     * @param bool $disableDrop
     * @return PeriodReportManager
     */
    public function setDisableDrop(bool $disableDrop): PeriodReportManager
    {
        $this->disableDrop = $disableDrop;
        return $this;
    }

    /**
     * @param CalendarGrade|null $grade
     * @return int
     */
    private function getGradeID(?CalendarGrade $grade): int
    {
        return $grade instanceof CalendarGrade ? $grade->getId() : 0 ;
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
    public function getMissingStudents(): ?Collection
    {
        return $this->missingStudents;
    }

    /**
     * setMissingStudents
     *
     * @return PeriodReportManager
     */
    public function setMissingStudents(): PeriodReportManager
    {
        if (empty($this->missingStudents))
            $this->missingStudents = new ArrayCollection();

        if ($this->getPossibleStudentCount() === 0)
            return $this;

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

        return $this;
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
    public function addMissingStudent(?Student $student, ?CalendarGrade $grade): PeriodReportManager
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
     * @param CalendarGrade $grade
     * @return Collection
     */
    public function getMissingStudentGrade(CalendarGrade $grade): Collection
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
        if (empty($this->getEntity()) || empty($this->getEntity()->getActivities()))
            return $this;

        foreach ($this->getEntity()->getActivities()->getIterator() as $activity)
            if ($activity->loadSpace())
                if (!$this->getAllocatedSpaces()->contains($activity->loadSpace()))
                    $this->allocatedSpaces->add($activity->loadSpace());
                else
                    $this->addDuplicateSpace(new SpaceActivity($activity->loadSpace(), $activity));

        return $this;
    }

    /**
     * @var Collection|null
     */
    private $duplicateSpaces;

    /**
     * @var int
     */
    private $duplicateSpaceCount = 0;

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
     * @param SpaceActivity|null $space
     * @return PeriodReportManager
     */
    public function addDuplicateSpace(?SpaceActivity $space): PeriodReportManager
    {
        if (empty($space) || $this->getDuplicateSpaces()->contains($space))
            return $this;

        $this->duplicateSpaces->add($space);
        $this->duplicateSpaceCount++;
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
        if (empty($this->getEntity()) || empty($this->getEntity()->getActivities()))
            return $this;

        foreach ($this->getEntity()->getActivities()->getIterator() as $activity)
            if ($activity->loadTutors())
            {
                foreach($activity->loadTutors()->getIterator() as $at)
                {
                    $this->addAllocatedTutor(new TutorActivity($at->getTutor(), $activity));
                }
            }
        return $this;
    }

    /**
     * @param TutorActivity|null $tutor
     * @return PeriodReportManager
     */
    public function addAllocatedTutor(?TutorActivity $tutor): PeriodReportManager
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
     * @param TutorActivity|null $tutor
     * @return PeriodReportManager
     */
    public function addDuplicateTutor(?TutorActivity $tutor): PeriodReportManager
    {
        if (empty($tutor) || $this->getDuplicateTutors()->contains($tutor))
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
     * writeReport
     *
     * @return PeriodReportManager
     */
    public function writeReport(): PeriodReportManager
    {
        return $this->writeStudentReport()
            ->writeSpaceReport()
            ->writeTutorReport()
            ->writeSummaryReport();
    }

    /**
     * writeStudentReport
     *
     * @return PeriodReportManager
     */
    private function writeStudentReport(): PeriodReportManager
    {
        // Missing Students
        if ($this->getMissingStudentCount() > 0)
        {
            $this->addMessage('warning', 'report.students.missing', ['transChoice' => $this->getMissingStudentCount()]);
            foreach($this->getMissingStudents()->getIterator() as $id=>$students)
            {
                $grade = $this->getGrades()[$id];
                $this->addMessage('info', 'report.grade.name', ['%grade%' => $grade->getFullName()]);
                foreach($students->getIterator() as $student)
                    $this->addMessage('light', 'report.student.details', ['%identifier%' => $student->getIdentifier(), '%name%' => $student->getFullName()]);
            }
        }

        // Duplicated Students
        if ($this->getDuplicateStudentCount() > 0)
        {
            $this->addMessage('danger', 'report.students.duplicated', ['transChoice' => $this->getDuplicateStudentCount()]);
            foreach($this->getDuplicateStudents()->getIterator() as $id=>$students)
            {
                $grade = $this->getGrades()[$id];
                $this->addMessage('info', 'report.grade.name', ['%grade%' => $grade->getFullName()]);
                foreach($students->getIterator() as $student)
                    $this->addMessage('light', 'report.student.details', ['%identifier%' => $student->getIdentifier(), '%name%' => $student->getFullName()]);
            }
        }

        return $this;
    }

    /**
     * getDuplicateTutorCount
     *
     * @return int
     */
    public function getDuplicateTutorCount(): int
    {
        return $this->duplicateTutorCount;
    }

    /**
     * getDuplicateStudentCount
     *
     * @return int
     */
    public function getDuplicateStudentCount(): int
    {
        return $this->duplicateStudentCount;
    }

    /**
     * writeSpaceReport
     *
     * @return PeriodReportManager
     */
    private function writeSpaceReport(): PeriodReportManager
    {
        // Duplicate Spaces
        if ($this->getDuplicateSpaceCount() > 0) {
            foreach ($this->getDuplicateSpaces()->getIterator() as $space) {
                $this->addMessage('danger', 'report.space.duplicated', ['%{name}' => $space->getSpace()->getName(), '%{activity}' => $space->getActivity()->getActivity()->getFullName()]);
                dump($space);
            }
        }

        // No space allocated.
        foreach($this->getActivities()->getIterator() as $activityReport)
        {
            if ($activityReport->hasSpace()) {

                if ($activityReport->getEntity()->loadSpace()->getCapacity() > 0 && $activityReport->getEntity()->loadSpace()->getCapacity() < $activityReport->getEntity()->getActivity()->getStudents()->count())
                    $this->addMessage('warning', 'report.activity.space.small', ['%{name}' => $activityReport->getEntity()->getFullName(), '%{space}' => $activityReport->getEntity()->loadSpace()->getFullName(), '%{count}' => $activityReport->getEntity()->getActivity()->getStudents()->count()]);
                continue;
            }
            $this->addMessage('warning', 'report.activity.space.missing', ['%name%' => $activityReport->getEntity()->getFullName()]);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getDuplicateSpaceCount(): int
    {
        return $this->duplicateSpaceCount;
    }

    /**
     * writeTutorReport
     *
     * @return PeriodReportManager
     */
    private function writeTutorReport(): PeriodReportManager
    {
        // Duplicate Tutors
        if ($this->getDuplicateTutorCount() > 0)
        {
            foreach($this->getDuplicateTutors()->getIterator() as $tutor)
                $this->addMessage('danger', 'report.tutor.duplicate', ['%name%' => $tutor->getTutor()->getFullName(), '%activity%' => $tutor->getActivity()->getActivity()->getFullName()]);
        }

        // No tutors allocated.
        foreach($this->getActivities()->getIterator() as $activityReport)
        {
            if ($activityReport->hasTutors())
                continue;
            $this->addMessage('warning', 'report.activity.tutor.missing', ['%name%' => $activityReport->getEntity()->getFullName()]);
        }
        return $this;
    }

    private function writeSummaryReport(): PeriodReportManager
    {
        if ($this->getStatus() === 'default' && count($this->getMessages()) === 0)
            $this->addMessage('success', 'report.period.ok');
        return $this;
    }

    /**
     * getGrade
     *
     * @param int $id
     * @return CalendarGrade
     */
    public function getGrade(int $id): CalendarGrade
    {
        $grades = $this->getGrades();
        if (isset($grades[$id]) && $id !== 0)
            return $grades[$id];
        $grade = new CalendarGrade();
        return $grade->setGrade('Empty')->setCalendar(CalendarManager::getCurrentCalendar());
    }
}