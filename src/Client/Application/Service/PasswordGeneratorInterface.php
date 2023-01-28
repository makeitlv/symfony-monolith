<?php

declare(strict_types=1);

namespace App\Client\Application\Service;

use App\Client\Domain\Entity\ValueObject\Password;

interface PasswordGeneratorInterface
{
    public function generate(int $length = 10): Password;
}
