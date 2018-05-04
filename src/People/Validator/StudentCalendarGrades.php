<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\StudentCalendarGradesValidator;
use Symfony\Component\Validator\Constraint;

class StudentCalendarGrades extends Constraint
{
	public $message = 'student.calendar_grades.grade.validation.error';

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return StudentCalendarGradesValidator::class;
	}

	/**
	 * @return array
	 */
	public function getTargets(): array
	{
		return [self::CLASS_CONSTRAINT];
	}
}
