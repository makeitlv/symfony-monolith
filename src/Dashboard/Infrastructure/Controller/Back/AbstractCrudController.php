<?php

declare(strict_types=1);

namespace App\Dashboard\Infrastructure\Controller\Back;

use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

abstract class AbstractCrudController extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController
{
    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        throw new RuntimeException('Use command instead!');
    }

    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        throw new RuntimeException('Use command instead!');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        throw new RuntimeException('Use command instead!');
    }
}