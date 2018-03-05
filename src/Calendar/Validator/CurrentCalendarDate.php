<?php
namespace App\Calendar\Validator;

use App\Calendar\Validator\Constraints\CalendarDateValidator;
use Symfony\Component\Validator\Constraint;

class CurrentCalendarDate extends Constraint
{
	public $message = 'calendar.current.validation.not_valid_date';

	public $fields;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return CalendarDateValidator::class;
	}
}
