<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\TermDateValidator;
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
