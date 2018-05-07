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
}