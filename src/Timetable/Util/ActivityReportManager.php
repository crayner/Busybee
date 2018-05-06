<?php
namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\Space;
use App\Entity\TimetablePeriodActivity;

class ActivityReportManager extends ReportManager
{
    /**
     * ActivityReportManager constructor.
     * @param TimetablePeriodActivity $activity
     */
    public function __construct(TimetablePeriodActivity $activity)
    {
        $this->setActivity($activity);
    }

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
     * @param TimetablePeriodActivity|null $activity
     * @return \stdClass
     */
    public function getActivityStatus(): \stdClass
    {
        if (! $this->getActivity()->loadSpace() instanceof Space) {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning', 'period.activities.activity.space.missing', ['%{name}' => $this->getActivity()->getFullName()], 'Timetable');
        } else {
            if (isset($this->spaces[$this->getActivity()->loadSpace()->getName()])) {
                $act = $this->spaces[$this->getActivity()->loadSpace()->getName()];
                $this->status->class = ' alert-warning';
                $this->status->alert = 'warning';
                $this->getMessageManager()->add('warning','period.activities.activity.space.duplicate', ['%{space}' => $this->getActivity()->loadSpace()->getName(), '%{activity}' => $this->getActivity()->getFullName(), '%{activity2}' => $act->getFullName()], 'Timetable');
            }
            $this->spaces[$this->getActivity()->loadSpace()->getName()] = $this->getActivity()->getActivity();
        }

        if ($this->hasTutors($this->getActivity()))
        {
            foreach($this->getActivity()->loadTutors()->getIterator() as $tutor)
            {
                $id = $tutor->getTutor()->getId();
                if (isset($this->staff[$id]))
                {
                    $act = $this->staff[$id];
                    $this->status->class = ' alert-warning';
                    $this->status->alert = 'warning';
                    $this->getMessageManager()->add('warning','period.activities.activity.staff.duplicate', ['%{name}' => $tutor->getFullName(), '%{activity}' => $this->getActivity()->getFullName(), '%{activity2}' => $act->getFullName()], 'Timetable');
                }
                else
                    $this->staff[$id] = $this->getActivity()->getActivity();
            }
        }
        else
        {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning','period.activities.activity.staff.missing', ['%{name}' => $this->getActivity()->getFullName()], 'Timetable');
        }

        if (count($this->getMessageManager()->getMessages()) > 0) {
            $this->failedStatus[$this->status->id] = $this->status->alert = $this->getMessageManager()->getHighestLevel();
        }

        return $this->status;
    }
}