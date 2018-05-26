<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\TwigValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Twig extends Constraint
{
	public $message = 'twig.error';

	public $transDomain = 'System';

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return TwigValidator::class;
	}

}