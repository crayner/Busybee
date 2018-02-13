<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\UniqueStudentRollValidator;
use Symfony\Component\Validator\Constraint;

class UniqueStudentRoll extends Constraint
{
	public $message = 'unique.student.roll.error';

	public $transDomain = 'School';

	public function validatedBy()
	{
		return UniqueStudentRollValidator::class;
	}

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
