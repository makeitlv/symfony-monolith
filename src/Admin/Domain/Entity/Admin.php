<?php

declare(strict_types=1);

namespace App\Admin\Domain\Entity;

use App\Admin\Domain\Entity\ValueObject\Role;
use App\Client\Domain\Entity\ValueObject\ConfirmationToken;
use App\Client\Domain\Entity\ValueObject\Email;
use App\Client\Domain\Entity\ValueObject\Name;
use App\Client\Domain\Entity\ValueObject\Password;
use App\Client\Domain\Entity\ValueObject\Status;
use App\Common\Domain\Entity\Aggregate;
use App\Common\Domain\Entity\ValueObject\Uuid;
use DateTimeImmutable;
use DomainException;

class Admin extends Aggregate
{
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        private Uuid $uuid,
        private Email $email,
        private Name $name,
        private Password $password,
        private Role $role,
        private Status $status,
        private ?ConfirmationToken $confirmationToken = null
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = null;
    }

    public function update(Email $email, Name $name): void
    {
        if (!$this->email->equals($email)) {
            $this->email = $email;
        }

        if (!$this->name->equals($name)) {
            $this->name = $name;
        }

        $this->updatedAt = new DateTimeImmutable();
    }

    public function updatePassword(Password $password): void
    {
        $this->password = $password;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function activate(): void
    {
        if ($this->status === Status::ACTIVATED) {
            throw new DomainException('Admin is already activated.');
        }

        $this->status = Status::ACTIVATED;
        $this->confirmationToken = null;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function block(): void
    {
        if ($this->status === Status::BLOCKED) {
            throw new DomainException('Admin is already blocked.');
        }

        $this->status = Status::BLOCKED;
        $this->confirmationToken = null;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function equals(Admin $admin): bool
    {
        return $this->email->equals($admin->email);
    }
}
