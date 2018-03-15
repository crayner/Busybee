<?php
namespace App\Calendar\Entity;


abstract class CalendarGradeExtension
{
    public function getFullName(): string
    {
        return $this->getGrade() . ' ('.$this->getCalendar()->getName().')';
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        if ($this->getStudents()->count() > 0)
            return false;

        return true;
    }
}