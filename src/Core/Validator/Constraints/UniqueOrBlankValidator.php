<?php
namespace App\Core\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueOrBlankValidator extends ConstraintValidator
{
	/**
	 * @var EntityManagerInterface
	 */
	private $om;

	/**
	 * UniqueOrBlankValidator constructor.
	 *
	 * @param EntityManagerInterface $sm
	 */
	public function __construct(EntityManagerInterface $om)
	{
		$this->om = $om;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
		{
			$value = null;

			return;
		}

		$entity = $this->context->getObject();

		$where = 'p.' . $constraint->field . ' = :identifier';

		$result = $this->om->getRepository($constraint->data_class)->createQueryBuilder('p')
			->where($where)
			->andWhere('p.id != :id')
			->setParameter('identifier', $value)
			->setParameter('id', $entity->getId())
			->getQuery()
			->getResult();
		if (!empty($result))
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%string%', $value)
				->addViolation();
		}

		return;
	}
}