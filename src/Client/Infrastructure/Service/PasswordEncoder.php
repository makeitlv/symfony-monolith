<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\Service;

use App\Client\Application\Service\PasswordEncoderInterface;
use App\Client\Domain\Entity\ValueObject\Password;
use App\Client\Domain\Entity\ValueObject\PlainPassword;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class PasswordEncoder implements PasswordEncoderInterface
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory
    ) {
    }

    public function encode(PlainPassword $plainPassword): Password
    {
        return new Password(
            $this->passwordHasherFactory
                ->getPasswordHasher(PasswordAuthenticatedUserInterface::class)
                ->hash((string) $plainPassword),
            $plainPassword
        );
    }
}
