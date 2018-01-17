<?php
namespace App\Calendar\Validator\Constraints;

use App\Entity\SpecialDay;
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
	 * SpecialDayDateValidator constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
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
							->addViolation();

						return;
					}
			}
		foreach ($value as $key => $day)
		{
			if ($day->getType() == 'alter')
			{
				$ok = true;
				if (empty($day->getOpen()))
				{
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty')
						->addViolation();

					return;
				}
				if (empty($day->getStart()))
				{
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty')
						->addViolation();

					return;
				}
				if (empty($day->getFinish()))
				{
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty')
						->addViolation();

					return;
				}
				if (empty($day->getClose()))
				{
					$this->context->buildViolation('calendar.specialDay.error.timeEmpty')
						->addViolation();

					return;
				}
				$time  = array(
					'a' => $day->getOpen(),
					'b' => $day->getStart(),
					'c' => $day->getFinish(),
					'd' => $day->getClose(),
				);
				$ttime = $time;
				asort($ttime);
				if ($time !== $ttime)
				{
					$this->context->buildViolation('calendar.specialDay.error.timeInvalid')
						->addViolation();

					return;
				}
			}
			if ($key == '__name__' && empty($day->getName()))
				unset($value[$key]);
		}

		return $value;
	}
}