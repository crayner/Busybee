<?php
namespace App\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Yaml\Yaml;

class YamlValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$message = '';

		try
		{
			Yaml::parse($value);
		}
		catch (\Exception $e)
		{
			$message = $e->getMessage();
		}

		if (!empty($message))
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%systemMessage%', $message)
				->addViolation();
		}
	}
}