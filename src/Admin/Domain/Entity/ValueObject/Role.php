<?php

declare(strict_types=1);

namespace App\Admin\Domain\Entity\ValueObject;

enum Role: string
{
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
