<?php

declare(strict_types=1);

namespace App\Common\Domain\Entity\ValueObject;

// phpcs:ignoreFile
readonly class Uuid
{
    public function __construct(
        private string $uuid
    ) {
    }

    public function equals(Uuid $uuid): bool
    {
        return $this->uuid === $uuid->uuid;
    }

    public function __toString(): string
    {
        return $this->uuid;
    }
}
