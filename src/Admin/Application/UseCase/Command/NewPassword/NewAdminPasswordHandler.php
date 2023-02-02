<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\NewPassword;

use App\Admin\Domain\Entity\Admin;
use App\Admin\Domain\Repository\AdminRepositoryInterface;
use App\Client\Application\Service\PasswordGeneratorInterface;
use App\Common\Domain\Bus\Command\CommandHandlerInterface;
use DomainException;

class NewAdminPasswordHandler implements CommandHandlerInterface
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private PasswordGeneratorInterface $passwordGenerator
    ) {
    }

    public function __invoke(NewAdminPasswordCommand $command): void
    {
        $admin = $this->adminRepository->findByUuid($command->uuid);
        if (!$admin instanceof Admin) {
            throw new DomainException(
                sprintf('Admin not found! Uuid: %s.', (string) $command->uuid)
            );
        }

        $admin->updatePassword($this->passwordGenerator->generate());

        $this->adminRepository->persist($admin);
    }
}
