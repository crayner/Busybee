<?php
namespace App\Core\Validator;

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
		return 'nowhitespace_validator';
	}
}
