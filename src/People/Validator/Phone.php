<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\PhoneValidator;
use Symfony\Component\Validator\Constraint;

class Phone extends Constraint
{
	public $message = 'phone.number.error';

	public function validatedBy()
	{
		return PhoneValidator::class;
	}
}
