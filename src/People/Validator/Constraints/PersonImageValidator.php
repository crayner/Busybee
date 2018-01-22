<?php
namespace App\People\Validator\Constraints;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator;

class PersonImageValidator extends ImageValidator
{
	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		dump($this);
		if (empty($value))
			return;

		if ($value instanceof File && empty($value->getFilename()))
			return;

		parent::validate($value, $constraint);
	}
}
