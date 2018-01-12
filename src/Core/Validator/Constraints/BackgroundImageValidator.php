<?php

namespace App\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Validator\Constraints\ImageValidator;


class BackgroundImageValidator extends ImageValidator
{
	public $groups = array('Default');

	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		parent::validate($value, $constraint);
	}
}