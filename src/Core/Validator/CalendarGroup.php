<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\CalendarGroupValidator;
use Symfony\Component\Validator\Constraint;

class CalendarGroup extends Constraint
{
	public $message = 'calendar.group.error.duplicate';

	public $calendar;

	public $errorPath = 'calendarGroups';


	public function validatedBy()
	{
		return CalendarGroupValidator::class;
	}
}
