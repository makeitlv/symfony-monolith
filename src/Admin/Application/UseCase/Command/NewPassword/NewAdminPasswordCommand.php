<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\NewPassword;

use App\Common\Domain\Bus\Command\CommandInterface;
use App\Common\Domain\Entity\ValueObject\Uuid;

readonly class NewAdminPasswordCommand implements CommandInterface
{
    public Uuid $uuid;

    public function __construct(
        string $uuid
    ) {
        $this->uuid = new Uuid($uuid);
    }
}
