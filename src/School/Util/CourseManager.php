<?php
namespace App\School\Util;


use App\Entity\Course;

class CourseManager
{
    public function getDetails(?Course $course): string
    {
        if (empty($course)) return '';

        return 'Do stuff here';
    }
}