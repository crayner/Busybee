<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\NoWhiteSpaceValidator;
use Symfony\Component\Validator\Constraint;

class NoWhiteSpace extends Constraint
{
	/**
	 * @var string
	 */
	public $message = 'nowhitespace.error';

	/**
	 * @var bool
	 */
	public $repair = true;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return NoWhiteSpaceValidator::class;
	}
}
