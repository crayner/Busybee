<?php
namespace App\Calendar\Util;

use App\Entity\Calendar;

class CalendarGradeManager
{
    /**
     * @var $calendarManager
     */
    private $calendarManager;

    /**
     * CalendarGradeManager constructor.
     *
     * @param CalendarManager $calendarManager
     */
    public function __construct(CalendarManager $calendarManager)
    {
        $this->calendarManager = $calendarManager;
    }

    /**
     * @return int
     */
    public function getStudentCount(): int
    {
        return 0;
    }

    /**
     * @param Calendar $calendar
     * @return Calendar|null
     */
    public function getNextCalendar(Calendar $calendar): ?Calendar
    {
        return $this->calendarManager->getNextCalendar($calendar);
    }
}