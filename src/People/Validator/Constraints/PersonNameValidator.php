<?php

namespace App\People\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class PersonNameValidator extends ConstraintValidator
{
	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value->getPreferredName()))
			$value->setPreferredName($value->getFirstName());

		if (empty($value->getOfficialName()))
		{
			$value->setOfficialName($value->getFirstName() . ' ' . $value->getSurname());
		}
	}
}