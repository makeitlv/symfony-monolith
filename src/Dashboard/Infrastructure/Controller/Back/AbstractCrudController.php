<?php

declare(strict_types=1);

namespace App\Dashboard\Infrastructure\Controller\Back;

use App\Dashboard\Infrastructure\Settings\DashboardSettings;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use RuntimeException;
use Symfony\Component\Translation\TranslatableMessage;
use Throwable;

abstract class AbstractCrudController extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController
{
    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        $this->action($entityManager, $entityInstance, 'persist');
    }

    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        $this->action($entityManager, $entityInstance, 'update');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        $this->action($entityManager, $entityInstance, 'remove');
    }

    public function persist(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        throw new RuntimeException('Use command instead!');
    }

    public function update(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        throw new RuntimeException('Use command instead!');
    }

    public function remove(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        throw new RuntimeException('Use command instead!');
    }

    private function action(EntityManagerInterface $entityManager, mixed $entityInstance, string $type): void
    {
        try {
            match ($type) {
                'persist' => $this->persist($entityManager, $entityInstance),
                'update' => $this->update($entityManager, $entityInstance),
                'remove' => $this->remove($entityManager, $entityInstance)
            };

            $message = match ($type) {
                'persist' => 'Content "%name%" has been created!',
                'update' => 'Content "%name%" has been updated!',
                'remove' => 'Content "%name%" has been deleted!'
            };

            $this->addFlash(
                'success',
                new TranslatableMessage(
                    $message,
                    ['%name%' => (string) $entityInstance],
                    DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                )
            );
        } catch (DomainException $exception) {
            $this->addFlash(
                'danger',
                new TranslatableMessage(
                    $exception->getMessage(),
                    [],
                    DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                )
            );
        } catch (Throwable $exception) {
            if ($exception instanceof RuntimeException) {
                throw $exception;
            }

            $this->addFlash(
                'danger',
                new TranslatableMessage(
                    'Something went wrong! Please, contact with administrator to figure out.',
                    [],
                    DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                )
            );
        }
    }
}
