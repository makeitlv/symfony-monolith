<?php

declare(strict_types=1);

namespace App\Client\Application\Service;

use App\Client\Domain\Entity\ValueObject\ConfirmationToken;

interface ConfirmationTokenGeneratorInterface
{
    public function generate(): ConfirmationToken;
}
