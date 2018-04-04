<?php
namespace App\Timetable\Validator;

use App\Timetable\Validator\Constraints\PeriodsValidator;
use Symfony\Component\Validator\Constraint;

class Periods extends Constraint
{
    /**
     * @var array
     */
    public $message;

    /**
     * Periods constructor.
     */
    public function __construct()
    {
        $this->message = [];
        $this->message['overlap'] = 'periods.constraint.overlap';
        $this->message['break'] = 'periods.constraint.break';
        $this->message['order'] = 'periods.constraint.order';
        $this->message['early'] = 'periods.constraint.early';
        $this->message['late'] = 'periods.constraint.late';
        $this->message['complete'] = 'periods.constraint.complete';
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return PeriodsValidator::class;
    }
}
