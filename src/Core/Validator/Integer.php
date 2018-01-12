<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\IntegerValidator;
use Symfony\Component\Validator\Constraint;

class Integer extends Constraint
{
	public $message = 'integer.invalid.message';

	public function validatedBy()
	{
		return IntegerValidator::class;
	}
}
