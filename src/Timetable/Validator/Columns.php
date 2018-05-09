<?php
namespace App\Timetable\Validator;

use App\Timetable\Validator\Constraints\ColumnsValidator;
use Symfony\Component\Validator\Constraint;

class Columns extends Constraint
{
    public $message = 'timetable.columns.message';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return ColumnsValidator::class;
    }
}
