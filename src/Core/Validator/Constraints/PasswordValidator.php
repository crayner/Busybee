<?php
namespace App\Core\Validator\Constraints;

use App\Install\Manager\InstallManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordValidator extends ConstraintValidator
{
	/**
	 * @var InstallManager
	 */
	private $installManager;

	public function __construct(InstallManager $installManager)
	{
		$this->installManager = $installManager;
	}

	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		if (! $this->installManager->isPasswordValid($value, $constraint->details))
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%numbers%', $constraint->details->isPasswordNumbers() ? 'Yes' : 'No')
				->setParameter('%mixedCase%', $constraint->details->isPasswordMixedCase() ? 'Yes' : 'No')
				->setParameter('%specials%', $constraint->details->isPasswordSpecials() ? 'Yes' : 'No')
				->setParameter('%minLength%', $constraint->details->getPasswordMinLength())
				->setTranslationDomain($constraint->transDomain)
				->addViolation();
		}
	}
}