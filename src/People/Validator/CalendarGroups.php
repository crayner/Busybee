<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\CalendarGroupsValidator;
use Symfony\Component\Validator\Constraint;

class CalendarGroups extends Constraint
{
	public $message = 'student.grades.error';

	public function validatedBy()
	{
		return CalendarGroupsValidator::class;
	}
}
