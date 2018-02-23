<?php
namespace App\Core\Validator\Constraints;

use App\Core\Exception\Exception;
use App\Core\Manager\SettingManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SettingChoiceValidator extends ConstraintValidator
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * SettingValidator constructor.
	 *
	 * @param SettingManager $settingManager
	 */
	public function __construct(SettingManager $settingManager)
	{
		$this->settingManager = $settingManager;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		$s = [];

		$list = $this->settingManager->get($constraint->name);

		foreach ($list as $q => $w)
		{
			if (is_array($w) && empty($constraint->valueIn))
				$s = array_merge($s, $w);
			elseif (is_array($w) && ! empty($constraint->valueIn))
				$s[$q] = $w[$constraint->valueIn];
			else
				$s[$q] = $w;
		}

		foreach($s as $test_value)
			if ($value == $test_value)
				return;

		$this->context->buildViolation($constraint->message)
			->setParameter('%string%', $value)
			->setTranslationDomain($constraint->transDomain)
			->addViolation();
	}
}