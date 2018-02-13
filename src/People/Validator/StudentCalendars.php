<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\StudentCalendarsValidator;
use Symfony\Component\Validator\Constraint;

class StudentCalendars extends Constraint
{
	public $message = 'student.grades.error';

	public function validatedBy()
	{
		return StudentCalendarsValidator::class;
	}
}
