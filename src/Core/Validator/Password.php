<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\PasswordValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
	public $message = 'user.password.message';

	public $details;

	public function validatedBy()
	{
		return PasswordValidator::class;
	}
}