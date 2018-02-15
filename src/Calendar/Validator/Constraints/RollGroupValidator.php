<?php
namespace App\Calendar\Validator\Constraints;

use App\Entity\RollGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RollGroupValidator extends ConstraintValidator
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

        $calendarId = $constraint->calendar->getId();

        $xx = [];

        foreach($value as $key=>$roll)
        {
            $query  = $this->entityManager->getRepository(RollGroup::class)->createQueryBuilder('r')
                ->where('r.calendar = :calendar_id');
            if (empty($roll->getId()))
                $query->andWhere('r.id IS NOT NULL');
            else {
                $query->andWhere('r.id <> :roll_id')
                    ->setParameter('roll_id', $roll->getId());
            }
            $result = $query
                ->andWhere('r.name = :name')
                ->setParameter('calendar_id', $calendarId)
                ->setParameter('name', $roll->getName())
                ->getQuery()
                ->getResult();

            if (! empty($result))
                $this->context->buildViolation('roll_group.validation.name.duplicate', [])
                    ->setTranslationDomain('Calendar')
                    ->atPath('['.$key.'].name')
                    ->addViolation();

            $query  = $this->entityManager->getRepository(RollGroup::class)->createQueryBuilder('r')
                ->where('r.calendar = :calendar_id');
            if (empty($roll->getId()))
                $query->andWhere('r.id IS NOT NULL');
            else {
                $query->andWhere('r.id <> :roll_id')
                    ->setParameter('roll_id', $roll->getId());
            }
            $result = $query
                ->andWhere('r.nameShort = :name')
                ->setParameter('calendar_id', $calendarId)
                ->setParameter('name', $roll->getNameShort())
                ->getQuery()
                ->getResult();

            if (! empty($result))
                $this->context->buildViolation('roll_group.validation.name.duplicate', [])
                    ->setTranslationDomain('Calendar')
                    ->atPath('['.$key.'].nameShort')
                    ->addViolation();

            $xx['name'][$roll->getName()] = isset($xx['name'][$roll->getName()]) ? $xx['name'][$roll->getName()] + 1 : 1 ;
            $xx['nameShort'][$roll->getNameShort()] = isset($xx['nameShort'][$roll->getNameShort()]) ? $xx['nameShort'][$roll->getNameShort()] + 1 : 1 ;

            if ($xx['name'][$roll->getName()] > 1)
                if (! empty($result))
                    $this->context->buildViolation('roll_group.validation.name.duplicate', [])
                        ->setTranslationDomain('Calendar')
                        ->atPath('['.$key.'].name')
                        ->addViolation();
            if ($xx['nameShort'][$roll->getNameShort()] > 1)
                if (! empty($result))
                    $this->context->buildViolation('roll_group.validation.name.duplicate', [])
                        ->setTranslationDomain('Calendar')
                        ->atPath('['.$key.'].nameShort')
                        ->addViolation();
        }
	}
}