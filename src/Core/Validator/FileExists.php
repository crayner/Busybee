<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\FileExistsValidator;
use Symfony\Component\Validator\Constraint;

class FileExists extends Constraint
{
    public $message = 'file.exists.error';

    public $transDomain = 'validators';
	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return FileExistsValidator::class;
	}
}
