<?php
namespace App\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Yaml\Exception\ParseException;
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
			$x = Yaml::parse($value);
		}
		catch (ParseException $e)
		{
			$message = $e->getMessage();
		}

		if (!empty($message))
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%systemMessage%', $message)
                ->setTranslationDomain($constraint->transDomain)
				->addViolation();
			return;
		}

		if (!is_array($x)){
		    $x = explode("\n", $value);
            $this->context->buildViolation($constraint->message)
                ->setParameter('%systemMessage%', 'Unable to parse at line 1. (near "'.substr($x[0], -12).'"')
                ->setTranslationDomain($constraint->transDomain)
                ->addViolation();
            return;

        }
	}
}