<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * "type" и "quantity" не могут приходить одновременно.
 */
class QuantityTypeConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof QuantityTypeConstraint) {
            throw new UnexpectedTypeException($constraint, QuantityTypeConstraint::class);
        }

        $object = $this->context->getObject();

        if ($object->type !== null && $object->quantity !== null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

        if(!$object->type && !$object->quantity) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
