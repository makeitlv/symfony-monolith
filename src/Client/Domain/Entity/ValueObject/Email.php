<?php

declare(strict_types=1);

namespace App\Client\Domain\Entity\ValueObject;

// phpcs:ignoreFile
readonly class Email
{
    public function __construct(
        private string $email
    ) {
    }

    public function equals(Email $email): bool
    {
        return (string) $this === (string) $email;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
