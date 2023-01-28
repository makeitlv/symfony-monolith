<?php

declare(strict_types=1);

namespace App\Client\Domain\Entity\ValueObject;

readonly class Password
{
    public function __construct(
        private string $password,
        private PlainPassword $plainPassword
    ) {
    }

    public function __toString(): string
    {
        return $this->password;
    }

    public function getPlainPassword(): string
    {
        return (string) $this->plainPassword;
    }
}
