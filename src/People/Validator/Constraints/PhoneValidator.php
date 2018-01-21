<?php
namespace App\People\Validator\Constraints;

use App\Core\Manager\SettingManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneValidator extends ConstraintValidator
{
	private $sm;

	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm;
	}

	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$pattern = $this->sm->get('phone.validation', '#^\d+$#');

		if (preg_match($pattern, $value) !== 1)
		{
			$this->context->buildViolation($constraint->message)
				->setTranslationDomain('Person')
				->setParameter('{regex}', $pattern)
				->addViolation();
		}
	}
}