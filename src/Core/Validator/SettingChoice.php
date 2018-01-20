<?php
namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

class SettingChoice extends Constraint
{
	public $name;
	public $strict = true;
	public $extra_choices = [];  // Add additional choices not found in the setting.
	public $message = 'setting.choice.invalid';
	public $valueIn = null;

	public function validatedBy()
	{
		return SettingChoice::class;
	}

	public function getRequiredOptions()
	{
		return [
			'name',
		];
	}

	public function __construct($options = null)
	{
		parent::__construct($options);
	}
}