<?php
namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;

class PeriodReportManager extends ReportManager
{
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
        if (empty($this->period) || $this->period->isEqualTo($period)) {
            $this->period = $period;
            $this->setChangedPeriod(true);
        }
        $this->generateActivityReports();
        return $this;
    }

    /**
     * @var ArrayCollection
     */
    private $activities;

    /**
     * @return ArrayCollection
     */
    public function getActivities(): ArrayCollection
    {
        if (empty($this->activities))
            $this->activities = new ArrayCollection();
        return $this->activities;
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return PeriodReportManager
     */
    public function addActivity(TimetablePeriodActivity $activity): PeriodReportManager
    {
        $report = new ActivityReportManager();
        $report->setActivity($activity);
        $this->getActivities()->set($activity->getId(), $report);
        return $this;
    }

    /**
     * @return PeriodReportManager
     */
    public function generateActivityReports(): PeriodReportManager
    {
        foreach($this->getPeriod()->getActivities()->getIterator() as $activity)
            $this->addActivity($activity);
        return $this;
    }

    /**
     * @var bool
     */
    private $changedPeriod = false;

    /**
     * @return bool
     */
    public function isChangedPeriod(): bool
    {
        return $this->changedPeriod;
    }

    /**
     * @param bool $changedPeriod
     * @return PeriodReportManager
     */
    public function setChangedPeriod(bool $changedPeriod): PeriodReportManager
    {
        $this->changedPeriod = $changedPeriod;
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
     * @param bool $disableDrop
     * @return PeriodReportManager
     */
    public function setDisableDrop(bool $disableDrop): PeriodReportManager
    {
        $this->disableDrop = $disableDrop;
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
        return $this->grades;
    }

    /**
     * @param ArrayCollection $grades
     * @return PeriodReportManager
     */
    public function setGrades(ArrayCollection $grades): PeriodReportManager
    {
        $this->grades = $grades;
        return $this;
    }
}