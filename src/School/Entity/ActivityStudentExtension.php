<?php
namespace App\School\Entity;

use App\Entity\Student;

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

    public function getFullStudentName($options = []): string
    {
        try {
            return $this->getStudent()->getFullname($options);
        } catch (\Exception $e) {
            dump($e);
            return '';
        }
        return '';
    }
}