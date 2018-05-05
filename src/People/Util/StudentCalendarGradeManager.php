<?php
namespace App\People\Util;

use App\Entity\CalendarGradeStudent;
use App\Entity\Student;
use Hillrange\Form\Util\CollectionManager;

class StudentCalendarGradeManager extends CollectionManager
{

    /**
     * @return string
     */
    public function getParentClass(): string
    {
        return Student::class;
    }

    /**
     * @return string
     */
    public function getChildClass(): string
    {
        return CalendarGradeStudent::class;
    }

    /**
     * @return string
     */
    public function removeChildMethod(): string
    {
        return 'removeCalendarGrade';
    }

    /**
     * @return bool
     */
    public function deleteChild(): bool
    {
        return true;
    }
}