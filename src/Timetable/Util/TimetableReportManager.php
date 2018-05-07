<?php
namespace App\Timetable\Util;


use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\Timetable;
use App\Entity\TimetablePeriod;
use App\Pagination\PeriodPagination;
use Doctrine\Common\Collections\ArrayCollection;

class TimetableReportManager
{
    /**
     * @var Timetable
     */
    private $timetable;

    /**
     * @return Timetable
     */
    public function getTimetable(): Timetable
    {
        return $this->timetable;
    }

    /**
     * @param Timetable $timetable
     * @return TimetableReportManager
     */
    public function setTimetable(Timetable $timetable): TimetableReportManager
    {
        if (empty($this->timetable) || ! $this->timetable->isEqualTo($timetable)) {
            $this->timetable = $timetable;
            $this->setChangedTimetable(true);
        }
        return $this;
    }

    /**
     * @var ArrayCollection|null
     */
    private $periods;

    /**
     * @return ArrayCollection
     */
    public function getPeriods(): ArrayCollection
    {
        if (empty($this->periods))
            $this->periods = new ArrayCollection();
        return $this->periods;
    }

    /**
     * @param PeriodPagination $pag
     * @return TimetableReportManager
     */
    public function setPeriodList(PeriodPagination $pag): TimetableReportManager
    {
        if ($this->isChangedTimetable())
            $this->periods = new ArrayCollection();

        foreach($pag->getResult() as $period)
            $this->addPeriod($period['entity']);

        return $this;
    }

    /**
     * @param TimetablePeriod $period
     * @return TimetableReportManager
     */
    public function addPeriod(TimetablePeriod $period): TimetableReportManager
    {
        $report = $this->getPeriods()->containsKey($period->getId()) ? $this->getPeriods()->get($period->getId()) : new PeriodReportManager();
        $report
            ->setGrades($this->getGrades())
            ->setCalendar($this->getCalendar())
            ->setPeriod($period);
        $this->getPeriods()->set($period->getId(), $report);
        return $this;
    }

    /**
     * @param $id
     * @return PeriodReportManager
     */
    public function getPeriod($id): PeriodReportManager
    {
        $period = $this->getPeriods()->get($id);
        return $period;
    }

    /**
     * @var bool
     */
    private $changedTimetable = false;

    /**
     * @return bool
     */
    public function isChangedTimetable(): bool
    {
        return $this->changedTimetable;
    }

    /**
     * @param bool $changedTimetable
     * @return TimetableReportManager
     */
    public function setChangedTimetable(bool $changedTimetable): TimetableReportManager
    {
        $this->changedTimetable = $changedTimetable;
        return $this;
    }

    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        if (empty($this->grades))
            throw new \InvalidArgumentException('The grades need to be injected into the report.');
        return $this->grades;
    }

    /**
     * @param ArrayCollection $grades
     * @return TimetableReportManager
     */
    public function setGrades(ArrayCollection $grades): TimetableReportManager
    {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @param TimetablePeriod $period
     * @return PeriodReportManager
     */
    public function getFullPeriodReport(TimetablePeriod $period): PeriodReportManager
    {
        $this->addPeriod($period);

        return $this->getPeriod($period->getId());
    }

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        if (empty($this->calendar))
            throw new \InvalidArgumentException('Injection of the current calendar is required.');
        return $this->calendar;
    }

    /**
     * @param Calendar $calendar
     * @return TimetableReportManager
     */
    public function setCalendar(Calendar $calendar): TimetableReportManager
    {
        $this->calendar = $calendar;
        return $this;
    }
}