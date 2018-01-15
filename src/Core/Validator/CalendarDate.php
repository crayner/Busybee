<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\CalendarDateValidator;
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
