<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\Repository;

use App\Admin\Domain\Entity\Admin;
use App\Admin\Domain\Repository\AdminRepositoryInterface;
use App\Client\Domain\Entity\ValueObject\Email;
use App\Common\Domain\Entity\ValueObject\Uuid;
use App\Common\Infrastructure\Repository\AbstractDoctrineRepository;

class AdminDoctrineRepository extends AbstractDoctrineRepository implements AdminRepositoryInterface
{
    protected const CLASS_NAME = Admin::class;

    public function persist(Admin $admin): void
    {
        $this->entityManager->persist($admin);
    }

    public function remove(Admin $admin): void
    {
        $this->entityManager->remove($admin);
    }

    public function findByUuid(Uuid $uuid): ?Admin
    {
        $admin = $this->objectRepository->findOneBy(['uuid.uuid' => (string) $uuid]);

        if (!$admin instanceof Admin) {
            return null;
        }

        return $admin;
    }

    public function findByEmail(Email $email): ?Admin
    {
        $admin = $this->objectRepository->findOneBy(['email.email' => (string) $email]);

        if (!$admin instanceof Admin) {
            return null;
        }

        return $admin;
    }
}
