<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class QuantityTypeConstraint extends Constraint
{
    public string $message = '“type” и “quantity” не могут приходить одновременно.';
    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT; // Specify that this constraint is for classes
    }
}
