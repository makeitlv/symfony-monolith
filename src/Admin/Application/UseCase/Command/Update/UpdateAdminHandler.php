<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\Update;

use App\Admin\Domain\Entity\Admin;
use App\Admin\Domain\Repository\AdminRepositoryInterface;
use App\Common\Domain\Bus\Command\CommandHandlerInterface;
use DomainException;

class UpdateAdminHandler implements CommandHandlerInterface
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository
    ) {
    }

    public function __invoke(UpdateAdminCommand $command): void
    {
        $admin = $this->adminRepository->findByUuid($command->uuid);
        if (!$admin instanceof Admin) {
            throw new DomainException(
                sprintf('Admin not found! Uuid: %s.', (string) $command->uuid)
            );
        }

        $existingAdmin = $this->adminRepository->findByEmail($command->email);
        if ($existingAdmin && $admin->equals($existingAdmin) === false) {
            throw new DomainException(
                sprintf('Admin already exists with such email %s.', (string) $command->email)
            );
        }

        $admin->update($command->email, $command->name);

        $this->adminRepository->persist($admin);
    }
}
