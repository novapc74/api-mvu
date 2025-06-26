<?php

namespace App\Model\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

readonly class UserRegisterDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Почта - обязательное поле.')]
        #[Assert\Email(message: 'Формат строки - Email.')]
        public string $email,

        #[Assert\NotBlank(message: 'Пароль - обязательное поле.')]
        public string $password,

        #[Assert\NotBlank(message: 'Подтвердить пароль - обязательное поле.')]
        public string $confirm_password,
    )
    {
    }

    #[Assert\Callback]
    public static function validate(mixed $value, ExecutionContextInterface $context, mixed $payload): void
    {
        if ($value->password !== $value->confirm_password) {
            $context->buildViolation('Пароли должны совпадать.')
                ->atPath('confirm_password')
                ->addViolation();
        }
    }
}
