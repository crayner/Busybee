<?php
namespace App\School\Entity;

class ActivityTutorExtension
{
    /**
     * @return bool
     * @todo Activity Tutor Can Delete
     */
    public function canDelete(): bool
    {
        return true;
    }
}