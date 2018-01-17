<?php
namespace App\Calendar\Validator;

use App\Calendar\Validator\Constraints\CalendarGroupValidator;
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
