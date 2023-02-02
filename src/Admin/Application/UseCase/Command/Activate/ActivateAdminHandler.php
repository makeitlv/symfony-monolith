<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\Activate;

use App\Admin\Domain\Entity\Admin;
use App\Admin\Domain\Repository\AdminRepositoryInterface;
use App\Common\Domain\Bus\Command\CommandHandlerInterface;
use DomainException;

class ActivateAdminHandler implements CommandHandlerInterface
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository
    ) {
    }

    public function __invoke(ActivateAdminCommand $command): void
    {
        $admin = $this->adminRepository->findByUuid($command->uuid);
        if (!$admin instanceof Admin) {
            throw new DomainException(
                sprintf('Admin not found! Uuid: %s.', (string) $command->uuid)
            );
        }

        $admin->activate();

        $this->adminRepository->persist($admin);
    }
}
