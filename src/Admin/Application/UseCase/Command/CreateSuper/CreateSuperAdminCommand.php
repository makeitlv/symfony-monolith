<?php

declare(strict_types=1);

namespace App\Admin\Application\UseCase\Command\CreateSuper;

use App\Client\Domain\Entity\ValueObject\Email;
use App\Client\Domain\Entity\ValueObject\Name;
use App\Client\Domain\Entity\ValueObject\PlainPassword;
use App\Common\Domain\Bus\Command\CommandInterface;
use App\Common\Domain\Entity\ValueObject\Uuid;

// phpcs:ignoreFile
readonly class CreateSuperAdminCommand implements CommandInterface
{
    public Uuid $uuid;
    public Email $email;
    public Name $name;
    public PlainPassword $plainPassword;

    public function __construct(
        string $uuid,
        string $email,
        string $firstname,
        string $lastname,
        string $plainPassword
    ) {
        $this->uuid = new Uuid($uuid);
        $this->email = new Email($email);
        $this->name = new Name($firstname, $lastname);
        $this->plainPassword = new PlainPassword($plainPassword);
    }
}
