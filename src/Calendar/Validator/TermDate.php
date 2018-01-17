<?php
namespace App\Calendar\Validator;

use App\Calendar\Validator\Constraints\TermDateValidator;
use Symfony\Component\Validator\Constraint;

class TermDate extends Constraint
{
	public $message = 'year.term.error.date';

	public $calendar;

	public function validatedBy()
	{
		return TermDateValidator::class;
	}
}
