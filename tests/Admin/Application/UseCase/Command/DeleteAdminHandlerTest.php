<?php

declare(strict_types=1);

namespace App\Tests\Admin\Application\UseCase\Command;

use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Admin\Application\UseCase\Command\Delete\DeleteAdminCommand;
use App\Tests\TestCase\ApplicationTestCase;
use Symfony\Component\Uid\Uuid;
use DomainException;

class DeleteAdminHandlerTest extends ApplicationTestCase
{
    public function testSuccessfulAdminDeleted(): void
    {
        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid = Uuid::v4()->__toString(),
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $this->commandBus->dispatch(new DeleteAdminCommand($uuid));

        $admin = $this->queryBuilder
            ->select('*')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchAssociative();

        self::assertFalse($admin);
    }

    public function testAdminCannotBeDeletedAsItIsNotExists(): void
    {
        $uuid = Uuid::v4()->__toString();

        try {
            $this->commandBus->dispatch(new DeleteAdminCommand($uuid));
        } catch (DomainException $exception) {
            self::assertEquals(
                sprintf('Admin not found! Uuid: %s.', $uuid),
                $exception->getMessage()
            );
        }
    }
}
