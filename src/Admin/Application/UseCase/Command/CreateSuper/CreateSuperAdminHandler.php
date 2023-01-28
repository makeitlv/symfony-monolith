<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\CreateSuper;

use App\Admin\Domain\Entity\Admin;
use App\Admin\Domain\Entity\ValueObject\Role;
use App\Admin\Domain\Repository\AdminRepositoryInterface;
use App\Client\Application\Service\PasswordEncoderInterface;
use App\Client\Domain\Entity\ValueObject\Status;
use App\Common\Domain\Bus\Command\CommandHandlerInterface;
use DomainException;

class CreateSuperAdminHandler implements CommandHandlerInterface
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private PasswordEncoderInterface $passwordEncoder
    ) {
    }

    public function __invoke(CreateSuperAdminCommand $command): void
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
            $this->passwordEncoder->encode($command->plainPassword),
            Role::ROLE_SUPER_ADMIN,
            Status::ACTIVATED
        );

        $this->adminRepository->persist($admin);
    }
}
