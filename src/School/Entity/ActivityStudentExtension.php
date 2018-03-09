<?php
namespace App\School\Entity;


class ActivityStudentExtension
{
    /**
     * @return bool
     * @todo Activity Student Can Delete
     */
    public function canDelete(): bool
    {
        return true;
    }
}