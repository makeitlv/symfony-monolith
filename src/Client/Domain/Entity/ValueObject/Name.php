<?php

declare(strict_types=1);

namespace App\Client\Domain\Entity\ValueObject;

// phpcs:ignoreFile
readonly class Name
{
    public function __construct(
        private string $firstname,
        private string $lastname,
    ) {
    }

    public function equals(Name $name): bool
    {
        return $this->firstname === $name->firstname && $this->lastname === $name->lastname;
    }

    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
