<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\Create;

use App\Admin\Domain\Entity\Admin;
use App\Admin\Domain\Entity\ValueObject\Role;
use App\Admin\Domain\Repository\AdminRepositoryInterface;
use App\Client\Application\Service\ConfirmationTokenGeneratorInterface;
use App\Client\Application\Service\PasswordEncoderInterface;
use App\Client\Application\Service\PasswordGeneratorInterface;
use App\Client\Domain\Entity\ValueObject\Status;
use App\Common\Domain\Bus\Command\CommandHandlerInterface;
use DomainException;

class CreateAdminHandler implements CommandHandlerInterface
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private PasswordGeneratorInterface $passwordGenerator,
        private ConfirmationTokenGeneratorInterface $confirmationTokenGenerator
    ) {
    }

    public function __invoke(CreateAdminCommand $command): void
    {
        if ($this->adminRepository->findByEmail($command->email)) {
            throw new DomainException(
                sprintf('Admin already exists with such email %s.', (string) $command->email)
            );
        }

        $admin = new Admin(
            $command->uuid,
            $command->email,
            $command->name,
            $this->passwordGenerator->generate(),
            Role::ROLE_ADMIN,
            Status::DISABLED,
            $this->confirmationTokenGenerator->generate()
        );

        $this->adminRepository->persist($admin);
        dump('fbxc');
    }
}
