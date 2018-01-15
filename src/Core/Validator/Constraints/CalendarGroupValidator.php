<?php
namespace App\Core\Validator\Constraints;

use App\Entity\CalendarGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CalendarGroupValidator extends ConstraintValidator
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * GradeValidator constructor.
	 *
	 * @param EntityManagerInterface $objectManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		$years = $this->entityManager->getRepository(CalendarGroup::class)->findBy(['calendar' => $constraint->calendar->getId()], ['sequence' => 'ASC']);

		if (!empty($years))
			foreach ($years as $y)
			{
				if (!$value->contains($y))
					if (!$y->canDelete())
					{
						$this->context->buildViolation('calendar.group.error.delete', ['%grade%' => $y->getFullName()])
							->addViolation();

						return;
					}
			}

		$test   = [];
		$tutors = [];
		foreach ($value as $q => $group)
		{
			$test[$group->getNameShort()] = isset($test[$group->getNameShort()]) ? $test[$group->getNameShort()] + 1 : 1;

			if ($test[$group->getNameShort()] > 1)
				$this->context->buildViolation('calendar.group.nameshort.unique', ['%grade%' => $group->getNameShort()])
					->atPath('[' . $q . '].nameShort')
					->addViolation();

			if (!is_null($group->getYearTutor()))
			{
				$tutors[$group->getYearTutor()->getId()] = empty($tutors[$group->getYearTutor()->getId()]) ? 1 : $tutors[$group->getYearTutor()->getId()] + 1;
				if ($tutors[$group->getYearTutor()->getId()] > 1)
					$this->context->buildViolation('calendar.group.yeartutor.unique', ['%{name}' => $group->getYearTutor()->formatName()])
						->atPath('[' . $q . '].yearTutor')
						->addViolation();
			}
		}
	}
}