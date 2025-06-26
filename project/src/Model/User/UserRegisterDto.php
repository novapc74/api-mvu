<?php

namespace App\Model\User;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserRegisterDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Почта - обязательное поле.')]
        public string $email,

        #[Assert\NotBlank(message: 'Пароль - обязательное поле.')]
        public string $password,
    )
    {
    }

}
