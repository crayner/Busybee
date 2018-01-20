<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\PersonNameValidator;
use Symfony\Component\Validator\Constraint;

class PersonName extends Constraint
{
	public $message = 'person.validator.preferredName.error';

	public $errorPath = 'preferredName';

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return PersonNameValidator::class;
	}

	/**
	 * @return array
	 */
	public function getTargets()
	{
		return array(self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT);
	}
}
