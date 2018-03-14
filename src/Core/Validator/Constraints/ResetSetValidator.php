<?php
namespace App\Core\Validator\Constraints;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ResetSetValidator extends ConstraintValidator
{
    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * ResetSetValidator constructor.
     * @param RequestStack $stack
     */
    public function __construct(RequestStack $stack)
    {
        $this->stack = $stack;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $request = $this->stack->getCurrentRequest()->request->all();

        if($this->context->getObject()->getParent() !== null)
            return;

        if (array_key_exists('Reset', $request))
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('home')
                ->addViolation();
    }
}