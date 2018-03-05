<?php
namespace App\Calendar\Validator\Constraints;

use App\Repository\CalendarRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use DateTime;

class CalendarDateValidator extends ConstraintValidator
{
	private $calendarRepository;

	public function __construct(CalendarRepository $calendarRepository)
	{
		$this->calendarRepository = $calendarRepository;
	}

	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		$calendar  = $constraint->fields;
		$start = $calendar->getFirstDay();
		$end   = $calendar->getLastDay();
		$name  = $calendar->getName();

		if (!$start instanceof DateTime || !$end instanceof DateTime)
		{
			$this->context->buildViolation('calendar.validation.invalid.format')
                ->setTranslationDomain('Calendar')
				->addViolation();
			return;
		}
		if ($start > $end)
		{
			$this->context->buildViolation('calendar.validation.invalid.order')
                ->setTranslationDomain('Calendar')
				->addViolation();
			return;
		}
		if ($start->diff($end)->y > 0)
		{
			$this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Calendar')
				->addViolation();
			return;
		}

		$calendars = $this->calendarRepository->createQueryBuilder('y')
			->where('y.id != :id')
			->setParameter('id', $calendar->getId())
			->getQuery()
			->getResult();

		if (is_array($calendars))
			foreach ($calendars as $calendar)
			{
				if ($calendar->getFirstDay() >= $start && $calendar->getFirstDay() <= $end)
				{
					$this->context->buildViolation('calendar.validation.overlapped', array('%name1%' => $calendar->getName(), '%name2%' => $name))
                        ->setTranslationDomain('Calendar')
						->addViolation();
					return;
				}
				if ($calendar->getLastDay() >= $start && $calendar->getLastDay() <= $end)
				{
					$this->context->buildViolation('calendar.validation.overlapped', array('%name1%' => $calendar->getName(), '%name2%' => $name))
                        ->setTranslationDomain('Calendar')
						->addViolation();
					return;
				}
			}
	}
}