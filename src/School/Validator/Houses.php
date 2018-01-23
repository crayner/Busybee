<?php
namespace App\School\Validator;

use App\School\Validator\Constraints\HousesValidator;
use Symfony\Component\Validator\Constraint;

class Houses extends Constraint
{
	public function validatedBy()
	{
		return HousesValidator::class;
	}
}