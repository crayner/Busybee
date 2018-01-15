<?php
namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

class TermDate extends Constraint
{
	public $message = 'year.term.error.date';

	public $calendar;

	public function validatedBy()
	{
		return 'term_date_validator';
	}
}
