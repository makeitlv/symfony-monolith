<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\Query;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table('admin')]
class Admin
{
    #[ORM\Id]
    #[ORM\Column]
    public string $uuid;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    #[Assert\Email]
    public string $email;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 64)]
    public string $firstname;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 64)]
    public string $lastname;

    #[ORM\Column]
    public string $role;

    #[ORM\Column]
    public string $status;

    #[ORM\Column]
    public string $password;

    #[ORM\Column]
    public string $confirmationToken;

    #[ORM\Column]
    public DateTimeImmutable $createdAt;

    #[ORM\Column]
    public DateTimeImmutable $updatedAt;
}
