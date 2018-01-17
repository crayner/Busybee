<?php
namespace App\Calendar\Validator;

use App\Calendar\Validator\Constraints\CalendarDateValidator;
use Symfony\Component\Validator\Constraint;

class CalendarDate extends Constraint
{
	public $message = 'calendar.error.date';

	public $fields;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return CalendarDateValidator::class;
	}
}
