<?php
namespace App\School\Validator;

use App\School\Validator\Constraints\TimesValidator;
use Symfony\Component\Validator\Constraint;

class Times extends Constraint
{
	public function validatedBy()
	{
		return TimesValidator::class;
	}

}