<?php
namespace App\Install\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GoogleOAuthValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		if (!$value->isGoogleOAuth())
			return;

		if (empty($value->getGoogleClientId()))
		{
			$this->context->buildViolation('google.client_id.empty')
				->atPath('googleClientId')
				->setTranslationDomain($constraint->transDomain)
				->addViolation();
		}
		if (empty($value->getGoogleClientSecret()))
		{
			$this->context->buildViolation('google.client_secret.empty')
				->atPath('googleClientSecret')
				->setTranslationDomain($constraint->transDomain)
				->addViolation();
		}
	}
}