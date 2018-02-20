<?php
namespace App\Calendar\Entity;


abstract class CalendarGradeExtension
{
    public function getFullName(): string
    {
        return $this->getGrade() . ' ('.$this->getCalendar()->getName().')';
    }
}