<?php
namespace App\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator;


class LogoValidator extends ImageValidator
{
	public $groups = array('Default');

	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		parent::validate($value, $constraint);
	}
}