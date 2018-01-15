<?php
namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

class CalendarStatus extends Constraint
{
	public $message = 'calendar.error.status';

	public $id;

	public function validatedBy()
	{
		return 'calendar_status_validator';
	}
}
