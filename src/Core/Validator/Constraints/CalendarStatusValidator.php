<?php
namespace App\Core\Validator\Constraints;

use App\Repository\CalendarRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;

class CalendarStatusValidator extends ConstraintValidatorBase
{
	/**
	 * @var CalendarRepository
	 */
	private $calendarRepository;

	/**
	 * CalendarStatusValidator constructor.
	 *
	 * @param CalendarRepository $calendarRepository
	 */
	public function __construct(CalendarRepository $calendarRepository)
	{
		$this->calendarRepository = $calendarRepository;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 *
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;


		if (empty($constraint->id))
		{
			throw new \InvalidArgumentException('ID is not set for Calendar Status validation.');
		}

		if ($value == 'current')
		{
			$xx = $this->calendarRepository->createQueryBuilder('c')
				->where('c.status = :status')
				->andWhere('c.id != :calendar_id')
				->setParameter('status', 'current')
				->setParameter('calendar_id', $constraint->id)
				->getQuery()
				->getOneOrNullResult();
			if (!is_null($xx) && $xx->getId() !== $constraint->id)
				$this->context->buildViolation($constraint->message)
					->addViolation();
		}

	}
}