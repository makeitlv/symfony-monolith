<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\Update;

use App\Client\Domain\Entity\ValueObject\Email;
use App\Client\Domain\Entity\ValueObject\Name;
use App\Common\Domain\Bus\Command\CommandInterface;
use App\Common\Domain\Entity\ValueObject\Uuid;

// phpcs:ignoreFile
readonly class UpdateAdminCommand implements CommandInterface
{
    public Uuid $uuid;
    public Email $email;
    public Name $name;

    public function __construct(
        string $uuid,
        string $email,
        string $firstname,
        string $lastname
    ) {
        $this->uuid = new Uuid($uuid);
        $this->email = new Email($email);
        $this->name = new Name($firstname, $lastname);
    }
}
