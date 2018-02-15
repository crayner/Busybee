<?php
namespace App\Calendar\Validator;

use App\Calendar\Validator\Constraints\RollGroupValidator;
use Symfony\Component\Validator\Constraint;

class RollGroup extends Constraint
{
	public $message = 'roll_group.validation.name.duplicate';

	public $calendar;

	public function validatedBy()
	{
		return RollGroupValidator::class;
	}
}
