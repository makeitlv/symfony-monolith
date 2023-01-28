<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\Service;

use App\Client\Application\Service\ConfirmationTokenGeneratorInterface;
use App\Client\Domain\Entity\ValueObject\ConfirmationToken;
use Exception;

class ConfirmationTokenGenerator implements ConfirmationTokenGeneratorInterface
{
    /**
     * @throws Exception
     */
    public function generate(): ConfirmationToken
    {
        return new ConfirmationToken(bin2hex(random_bytes(ConfirmationToken::TOKEN_LENGTH / 2)));
    }
}
