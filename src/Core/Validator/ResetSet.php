<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\ResetSetValidator;
use Symfony\Component\Validator\Constraint;

class ResetSet extends Constraint
{
    public $message = 'The form has been reset.';

    public $severity = 'info';

    public function validatedBy()
    {
        return ResetSetValidator::class;
    }
}