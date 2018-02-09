<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\YamlValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Yaml extends Constraint
{
	public $message = 'yaml.validation.error';

	public $transDomain = 'validators'   ;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return YamlValidator::class;
	}
}