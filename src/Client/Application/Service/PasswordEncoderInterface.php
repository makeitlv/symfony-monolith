<?php

declare(strict_types=1);

namespace App\Client\Application\Service;

use App\Client\Domain\Entity\ValueObject\Password;
use App\Client\Domain\Entity\ValueObject\PlainPassword;

interface PasswordEncoderInterface
{
    public function encode(PlainPassword $plainPassword): Password;
}
