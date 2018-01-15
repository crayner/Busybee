<?php
namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

class SpecialDayDate extends Constraint
{
	public $message = 'specialday.error.date';

	public $calendar;

	public function validatedBy()
	{
		return 'specialday_date_validator';
	}
}
