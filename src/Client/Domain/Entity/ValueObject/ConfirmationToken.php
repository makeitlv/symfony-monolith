<?php

declare(strict_types=1);

namespace App\Client\Domain\Entity\ValueObject;

// phpcs:ignoreFile
readonly class ConfirmationToken
{
    public const TOKEN_LENGTH = 32;

    public function __construct(
        private string $confirmationToken
    ) {
    }

    public function __toString(): string
    {
        return $this->confirmationToken;
    }
}
