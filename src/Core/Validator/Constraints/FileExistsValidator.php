<?php
namespace App\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class FileExistsValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

        if (class_exists($value) || file_exists($value))
            return;

        $this->context->buildViolation($constraint->message)
            ->setTranslationDomain($constraint->transDomain)
            ->addViolation();

    }
}