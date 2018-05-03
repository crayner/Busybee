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
		if (! $constraint->strict && empty($value))
			return;

		$s = [];

		$list = $this->settingManager->get($constraint->settingName, []);
        if (! is_array($list))
        {
            $this->context->buildViolation('setting.settings.missing')
                ->setParameter('%resource%', $constraint->settingName)
                ->setTranslationDomain('System')
                ->addViolation();
            return;
        }
		$list = array_merge($list, $constraint->extra_choices);

		foreach ($list as $q => $w)
		{
			if (is_array($w) && empty($constraint->settingDataValue))
				$s = array_merge($s, $w);
			elseif (is_array($w) && ! empty($constraint->settingDataValue))
				$s[$q] = $w[$constraint->settingDataValue];
			elseif ($constraint->useLabelAsValue)
                $s[$q] = $q;
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