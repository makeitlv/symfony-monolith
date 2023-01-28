<?php

declare(strict_types=1);

namespace App\Client\Domain\Entity\ValueObject;

readonly class PlainPassword
{
    public function __construct(
        private string $password
    ) {
    }

    public function __toString(): string
    {
        return $this->password;
    }
}
