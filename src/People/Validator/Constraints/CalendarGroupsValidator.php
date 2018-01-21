<?php
namespace App\People\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CalendarGroupsValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$current = 0;
		$year    = [];

		foreach ($value->toArray() as $q => $group)
		{
dump($group);
			if (empty($group->getStudent()) || empty($group->getCalendarGroup()))
			{

				$this->context->buildViolation('student.grades.error.empty')
					->setTranslationDomain('Person')
					->atPath('[' . strval($q) . ']')
					->atPath('calendarGroup')
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

			$gy = $group->getCalendar()->getName();

			if (!is_null($gy))
			{
				$year[$gy] = !isset($year[$gy]) ? 1 : $year[$gy] + 1;

				if ($year[$gy] > 1)
				{
					$this->context->buildViolation('student.grades.error.year')
						->atPath('[' . strval($q) . ']')
						->atPath('calendarGroup')
						->setTranslationDomain('Person')
						->addViolation();
				}
			}
		}
	}
}