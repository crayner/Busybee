<?php

namespace App\People\Validator\Constraints;

use App\Entity\Student;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class StudentCalendarGradesValidator extends ConstraintValidator
{
	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
	    if (empty($value) || ! $value instanceof Student)
	        return ;

	    $calendars = [];
	    $current = false;
	    foreach($value->getCalendarGrades()->getIterator() as $q=>$scg)
        {
            if (in_array($scg->getCalendarGrade()->getCalendar(), $calendars))
                $this->context->buildViolation($constraint->message)
                    ->atPath('calendarGrades['.$q.'].calendarGrade')
                    ->setParameter('%{grade}', $scg->getFullGradeName())
                    ->setParameter('%{calendar}', $scg->getCalendarGrade()->getCalendar()->getName())
                    ->setTranslationDomain('Student')
                    ->addViolation();
            else
                $calendars[] = $scg->getCalendarGrade()->getCalendar();

            if ($scg->getStatus() === 'current' && $current)
                $this->context->buildViolation('student.calendar_grades.status.validation.error')
                    ->atPath('calendarGrades['.$q.'].status')
                    ->setTranslationDomain('Student')
                    ->addViolation();
            if ($scg->getStatus() === 'current')
                $current = true;
        }
	}
}