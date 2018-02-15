<?php
namespace App\People\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StudentCalendarsValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$current = 0;
		$year    = [];

		foreach ($value->toArray() as $q => $group)
		{
			if (empty($group->getStudent()) || empty($group->getCalendarGroup()))
			{

				$this->context->buildViolation('student.grades.error.empty')
					->setTranslationDomain('Person')
					->atPath('[' . strval($q) . ']')
					->atPath('rollGroup')
					->addViolation();
			}

			if ($group->getStatus() === 'Current')
			{
				$current++;

				if ($current > 1)
				{
					$this->context->buildViolation('student.grades.error.current')
						->atPath('[' . strval($q) . ']')
						->atPath('status')
						->setTranslationDomain('Person')
						->addViolation();
				}
			}

			$gy = $group->getCalendarGroup()->getCalendar()->getName();

			if (!is_null($gy))
			{
				$year[$gy] = !isset($year[$gy]) ? 1 : $year[$gy] + 1;

				if ($year[$gy] > 1)
				{
					$this->context->buildViolation('student.grades.error.year')
						->atPath('[' . strval($q) . ']')
						->atPath('rollGroup')
						->setTranslationDomain('Person')
						->addViolation();
				}
			}
		}
	}
}