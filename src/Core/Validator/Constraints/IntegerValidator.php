<?php

namespace App\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class IntegerValidator extends ConstraintValidator
{
	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		if (intval($value) != $value)
			$this->context->buildViolation($constraint->message)
				->setParameter('%value%', $value)
				->setParameter('%type%', gettype($value))
				->addViolation();

	}
}