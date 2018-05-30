<?php
namespace App\School\Validator;

use App\Core\Validator\Constraints\SettingNameRequiredValidator;
use Symfony\Component\Validator\Constraint;

class Houses extends Constraint
{
    public $required = [
        'name',
        'shortName',
        'logo',
    ];

	public function validatedBy()
	{
		return SettingNameRequiredValidator::class;
	}
}