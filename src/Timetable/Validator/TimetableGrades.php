<?php
namespace App\Timetable\Validator;

use App\Timetable\Validator\Constraints\TimetableGradesValidator;
use Symfony\Component\Validator\Constraint;

class TimetableGrades extends Constraint
{
    public $message = 'timetable.grades.validate.message';

    public function validatedBy()
    {
        return TimetableGradesValidator::class;
    }
}