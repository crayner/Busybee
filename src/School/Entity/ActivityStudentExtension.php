<?php
namespace App\School\Entity;

use App\Entity\Calendar;
use App\Entity\CalendarGrade;

class ActivityStudentExtension
{
    /**
     * @return bool
     * @todo Activity Student Can Delete
     */
    public function canDelete(): bool
    {
        return false;
    }

    public function getFullStudentName($options = []): string
    {
        try {
            return $this->getStudent()->getFullname($options);
        } catch (\Exception $e) {
            return '';
        }
        return '';
    }

    /**
     * @param Calendar $calendar
     * @return null|string
     */
    public function getStudentCalendarGrades(Calendar $calendar): ?string
    {
        $grades = $this->getStudent()->getCalendarGrades();

        foreach ($grades as $grade)
            if ($grade->getCalendar()->getId() == $calendar->getId())
                return $grade->getGrade();

        return null;
    }
}