<?php
namespace App\Timetable\Entity;

class TimetableDayExtension
{
    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return false;
    }
}