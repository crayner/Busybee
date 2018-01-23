<?php
namespace App\Calendar\Validator\Constraints;

use App\Entity\SpecialDay;
use App\School\Util\DaysTimesManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SpecialDayDateValidator extends ConstraintValidator
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var DaysTimesManager
	 */
	private $daysTimesManager;

	/**
	 * SpecialDayDateValidator constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager, DaysTimesManager $daysTimesManager)
	{
		$this->entityManager = $entityManager;
		$this->daysTimesManager = $daysTimesManager;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 *
	 * @return mixed|void
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;
		$days = $this->entityManager->getRepository(SpecialDay::class)->findBy(['calendar' => $constraint->calendar->getId()], ['day' => 'ASC']);

		if (!empty($days))
			foreach ($days as $d)
			{
				if (!$value->contains($d))
					if (!$d->canDelete())
					{
						$this->context->buildViolation('calendar.specialDay.error.delete', ['%day%' => $d->getDay()->format('jS M/Y')])
							->setTranslationDomain('Calendar')
							->atPath('['.$value->indexOf($d).'].name')
							->addViolation();

						return;
					}
			}

		foreach ($value as $key => $day)
		{
			if ($day->getType() == 'alter')
			{
				if (empty($day->getOpen()))
				{
					$day->setOpen($this->daysTimesManager->getTime()->getOpen());
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty', ['%{date}' => $day->getDay()->format('jS M/Y'), '%{time_name}' => $this->daysTimesManager->getTime()->getTranslation('open'), '%{name}' => $day->getName()])
						->setTranslationDomain('Calendar')
						->atPath('['.$key.'].open')
						->addViolation();
				}

				if (empty($day->getStart()))
				{
					$day->setStart($this->daysTimesManager->getTime()->getBegin());
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty', ['%{date}' => $day->getDay()->format('jS M/Y'), '%{time_name}' => $this->daysTimesManager->getTime()->getTranslation('start'), '%{name}' => $day->getName()])
						->setTranslationDomain('Calendar')
						->atPath('['.$key.'].start')
						->addViolation();
				}

				if (empty($day->getFinish()))
				{
					$day->setFinish($this->daysTimesManager->getTime()->getFinish());
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty', ['%{date}' => $day->getDay()->format('jS M/Y'), '%{time_name}' => $this->daysTimesManager->getTime()->getTranslation('finish'), '%{name}' => $day->getName()])
						->setTranslationDomain('Calendar')
						->atPath('['.$key.'].finish')
						->addViolation();
				}

				if (empty($day->getClose()))
				{
					$day->setClose($this->daysTimesManager->getTime()->getClose());
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty', ['%{date}' => $day->getDay()->format('jS M/Y'), '%{time_name}' => $this->daysTimesManager->getTime()->getTranslation('close'), '%{name}' => $day->getName()])
						->setTranslationDomain('Calendar')
						->atPath('['.$key.'].close')
						->addViolation();
				}


				$time  = [
					'a' => $day->getOpen(),
					'b' => $day->getStart(),
					'c' => $day->getFinish(),
					'd' => $day->getClose(),
				];
				$ttime = $time;
				asort($ttime);
				if ($time !== $ttime)
				{
					$this->context->buildViolation('calendar.specialDay.error.timeInvalid')
						->setTranslationDomain('Calendar')
						->atPath('['.$key.'].open')
						->addViolation();

					return $value;
				}
			}
			if ($key == '__name__' && empty($day->getName()))
				unset($value[$key]);
		}

		return $value;
	}
}