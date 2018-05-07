<?php
namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;

class ActivityReportManager extends ReportManager
{
    /**
     * @var TimetablePeriodActivity
     */
    private $activity;

    /**
     * @return TimetablePeriodActivity
     */
    public function getActivity(): TimetablePeriodActivity
    {
        return $this->activity;
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return ActivityReportManager
     */
    public function setActivity(TimetablePeriodActivity $activity): ActivityReportManager
    {
        $this->activity = $activity;
        $this->isActivityActive();
        return $this;
    }

    /**
     * @param bool $disableDrop
     * @return ActivityReportManager
     */
    public function setDisableDrop(bool $disableDrop): ActivityReportManager
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
        if (empty($this->grades))
            throw new \InvalidArgumentException('The grades need to be injected into the report.');
        return $this->grades;
    }

    /**
     * @param ArrayCollection $grades
     * @return ActivityReportManager
     */
    public function setGrades(ArrayCollection $grades): ActivityReportManager
    {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @var bool
     */
    private $activityActive;
    /**
     * @return bool
     */
    public function isActivityActive(): bool
    {
        if (!is_null($this->activityActive))
            return $this->activityActive;

        $this->activityActive = false;

        foreach($this->getActivity()->getActivity()->getCalendarGrades() as $grade)
            if ($this->getGrades()->contains($grade))
                $this->activityActive = true;

        return $this->activityActive;
    }
}