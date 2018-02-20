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
     * @todo  Test canDelete
     */
    public function canDelete()
    {
        return false;
    }
}