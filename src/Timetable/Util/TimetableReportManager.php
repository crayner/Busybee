<?php
namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\Timetable;
use App\Pagination\PeriodPagination;

class TimetableReportManager extends ReportManager
{
    /**
     * TimetableReportManager constructor.
     * @param Timetable $timetable
     */
    public function __construct(Timetable $timetable, PeriodPagination $periodPagination)
    {
        $this->setTimetable($timetable);
        $this->periodPagination = $periodPagination;
    }

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
        $this->timetable = $timetable;
        return $this;
    }
}