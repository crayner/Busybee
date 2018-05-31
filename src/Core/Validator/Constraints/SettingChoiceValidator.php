<?php
namespace App\Core\Validator\Constraints;

use App\Core\Manager\SettingManager;
use App\Core\Util\SettingChoiceGenerator;
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

        if ($constraint->translation)
        {
            if (! empty($constraint->translation['transDomain']))
                $constraint->transDomain = $constraint->translation['transDomain'];
            if (! empty($constraint->translation['message']))
                $constraint->message = $constraint->translation['message'];
        }

		if (empty($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%{name}', $constraint->settingName)
                ->setParameter('%{value}', '')
                ->setTranslationDomain($constraint->transDomain)
                ->addViolation();
            return ;
        }

        if ($constraint->useLowerCase)
            $value = strtolower($value);
		$s = [];

		$list = $this->settingManager->get($constraint->settingName, []);

        if (! is_array($list) || empty($list))
        {
            $this->context->buildViolation('setting.settings.missing')
                ->setParameter('%resource%', $constraint->settingName)
                ->setTranslationDomain($constraint->transDomain)
                ->addViolation();
            return;
        }

        $list = SettingChoiceGenerator::generateChoices($constraint->settingName, $list, $constraint->settingDataName);

        $list = array_merge($list, $constraint->extra_choices);

dump([$constraint, $value, $list]);
		if (! in_array($value, $list))
            $this->context->buildViolation($constraint->message)
                ->setParameter('%{value}', $value)
                ->setParameter('%{name}', $constraint->settingName)
                ->setTranslationDomain($constraint->transDomain)
                ->addViolation();
	}
}