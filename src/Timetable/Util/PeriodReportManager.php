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

use App\Core\Util\ReportManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\TimetablePeriodActivity;
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
            $students = $activity->getAllocatedStudents();
            if ($students->count() === 0)
                continue;
            dump($students);die();
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
}