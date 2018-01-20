<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\PersonEmailValidator;
use Symfony\Component\Validator\Constraint;

class PersonEmail extends Constraint
{
	public $message = 'person.validator.email.unique';

	public $errorPath;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return PersonEmailValidator::class;
	}
}
