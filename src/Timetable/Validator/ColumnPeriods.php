<?php
namespace App\Timetable\Validator;

use App\Timetable\Validator\Constraints\ColumnPeriodsValidator;
use Symfony\Component\Validator\Constraint;

class ColumnPeriods extends Constraint
{
    public $message = '';

    public function validatedBy()
    {
        return ColumnPeriodsValidator::class;
    }

    /**
     * @return mixed
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}