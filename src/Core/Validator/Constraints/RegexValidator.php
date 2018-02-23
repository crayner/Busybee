<?php
namespace App\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegexValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$message = '';

		try
		{
			$test = preg_match($value, null);
			return ;
		}
		catch (\Exception $e)
		{
			$message = $e->getMessage();
		}

		if (!empty($message))
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%systemMessage%', $message)
                ->setTranslationDomain($constraint->transDomain)
				->addViolation();
		}
	}
}