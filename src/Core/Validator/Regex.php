<?php

namespace App\Core\Validator;

use App\Core\Validator\Constraints\RegexValidator;
use Symfony\Component\Validator\Constraint;

class Regex extends Constraint
{
	public $message = 'regex.error';

	public $transDomain = 'validator';

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return RegexValidator::class;
	}
}