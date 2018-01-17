<?php
namespace App\Calendar\Validator;

use App\Calendar\Validator\Constraints\SpecialDayDateValidator;
use Symfony\Component\Validator\Constraint;

class SpecialDayDate extends Constraint
{
	public $message = 'specialday.error.date';

	public $calendar;

	public function validatedBy()
	{
		return SpecialDayDateValidator::class;
	}
}
