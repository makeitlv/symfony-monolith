<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\Service;

use App\Client\Application\Service\PasswordGeneratorInterface;
use App\Client\Domain\Entity\ValueObject\Password;
use App\Client\Domain\Entity\ValueObject\PlainPassword;
use Exception;

class PasswordGenerator implements PasswordGeneratorInterface
{
    public function __construct(
        private PasswordEncoder $passwordEncoder
    ) {
    }

    /**
     * @throws Exception
     */
    public function generate(int $length = 8): Password
    {
        $plainPassword = substr(
            preg_replace(
                '/[^a-zA-Z0-9]/',
                '',
                base64_encode(bin2hex(random_bytes(32)))
            ),
            0,
            $length
        );

        return $this->passwordEncoder->encode(new PlainPassword($plainPassword));
    }
}
