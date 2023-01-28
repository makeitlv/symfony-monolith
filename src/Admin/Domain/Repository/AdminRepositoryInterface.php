<?php

declare(strict_types=1);

namespace App\Admin\Domain\Repository;

use App\Admin\Domain\Entity\Admin;
use App\Client\Domain\Entity\ValueObject\Email;
use App\Common\Domain\Entity\ValueObject\Uuid;

interface AdminRepositoryInterface
{
    public function persist(Admin $admin): void;
    public function remove(Admin $admin): void;
    public function findByUuid(Uuid $uuid): ?Admin;
    public function findByEmail(Email $email): ?Admin;
}
