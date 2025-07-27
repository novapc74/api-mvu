<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class QuantityTypeConstraint extends Constraint
{
    public string $message = 'Должно присутствовать либо “type” либо “quantity”';
    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
