<?php
namespace App\Timetable\Entity;

use App\Entity\Staff;

abstract class TimetablePeriodActivityTutorExtension
{
    /**
     * @return bool
     * @todo Activity Tutor Can Delete
     */
    public function canDelete(): bool
    {
        return false;
    }

    /**
     * @param array $options
     * @return string
     */
    public function getFullName(array $options = []): string
    {
        if ($this->getTutor() instanceof Staff)
            return $this->getTutor()->getFullName($options);
        return 'Error: Tutor not found!';
    }
}