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

		foreach ($value->toArray() as $q => $grade)
		{
			if (empty($grade->getStudent()) || empty($grade->getGrade()))
			{

				$this->context->buildViolation('student.grades.error.empty')
					->setTranslationDomain('BusybeePersonBundle')
					->atPath('[' . strval($q) . ']')
					->atPath('grade')
					->addViolation();
			}

			if ($grade->getStatus() === 'Current')
			{
				$current++;

				if ($current > 1)
				{
					$this->context->buildViolation('student.grades.error.current')
						->atPath('[' . strval($q) . ']')
						->atPath('status')
						->setTranslationDomain('BusybeePersonBundle')
						->addViolation();
				}
			}

			$gy = $grade->getYear()->getName();

			if (!is_null($gy))
			{
				$year[$gy] = !isset($year[$gy]) ? 1 : $year[$gy] + 1;

				if ($year[$gy] > 1)
				{
					$this->context->buildViolation('student.grades.error.year')
						->atPath('[' . strval($q) . ']')
						->atPath('grade')
						->setTranslationDomain('BusybeePersonBundle')
						->addViolation();
				}
			}
		}
	}
}