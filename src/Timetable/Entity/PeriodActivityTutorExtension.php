<?php
namespace App\Timetable\Entity;

class PeriodActivityTutorExtension
{
    /**
     * @return bool
     * @todo Activity Tutor Can Delete
     */
    public function canDelete(): bool
    {
        return false;
    }
}