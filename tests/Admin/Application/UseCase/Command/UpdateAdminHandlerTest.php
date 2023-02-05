<?php

declare(strict_types=1);

namespace App\Tests\Admin\Application\UseCase\Command;

use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Admin\Application\UseCase\Command\Update\UpdateAdminCommand;
use App\Tests\TestCase\ApplicationTestCase;
use Symfony\Component\Uid\Uuid;
use DomainException;

class UpdateAdminHandlerTest extends ApplicationTestCase
{
    public function testSuccessfulAdminUpdated(): void
    {
        $uuid = Uuid::v4()->__toString();

        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid,
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $oldAdminData = $this->queryBuilder
            ->select('*')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchAssociative();

        $this->commandBus->dispatch(new UpdateAdminCommand(
            $uuid,
            'new@new.com',
            'New',
            'New'
        ));

        $newAdminData = $this->queryBuilder
            ->select('*')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchAssociative();

        self::assertEquals($oldAdminData['uuid'], $newAdminData['uuid']);
        self::assertNotEquals($oldAdminData['email'], $newAdminData['email']);
        self::assertNotEquals($oldAdminData['firstname'], $newAdminData['firstname']);
        self::assertNotEquals($oldAdminData['lastname'], $newAdminData['lastname']);
    }

    public function testAdminCannotBeUpdatedAsIsNotExists(): void
    {
        $uuid = Uuid::v4()->__toString();

        try {
            $this->commandBus->dispatch(new UpdateAdminCommand(
                $uuid,
                'new@new.com',
                'New',
                'New'
            ));
        } catch (DomainException $exception) {
            self::assertEquals(
                sprintf('Admin not found! Uuid: %s.', $uuid),
                $exception->getMessage()
            );
        }
    }

    public function testAdminCannotBeUpdatedAsEmailAlreadyUsed(): void
    {
        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid = Uuid::v4()->__toString(),
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $this->commandBus->dispatch(new CreateAdminCommand(
            Uuid::v4()->__toString(),
            'new@new.com',
            'New',
            'New',
            'pswrd'
        ));

        try {
            $this->commandBus->dispatch(new UpdateAdminCommand(
                $uuid,
                'new@new.com',
                'New',
                'New'
            ));
        } catch (DomainException $exception) {
            self::assertEquals(
                'Admin already exists with such email new@new.com.',
                $exception->getMessage()
            );
        }
    }
}
