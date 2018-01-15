<?php
namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

class CalendarGroup extends Constraint
{
	public $message = 'calendar.group.error.duplicate';

	public $calendar;

	public $errorPath = 'calendarGroups';


	public function validatedBy()
	{
		return 'calendar_group_validator';
	}
}
