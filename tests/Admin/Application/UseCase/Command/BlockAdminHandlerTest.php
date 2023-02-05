<?php

declare(strict_types=1);

namespace App\Tests\Admin\Application\UseCase\Command;

use App\Admin\Application\UseCase\Command\Block\BlockAdminCommand;
use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Tests\TestCase\ApplicationTestCase;
use Symfony\Component\Uid\Uuid;
use DomainException;

class BlockAdminHandlerTest extends ApplicationTestCase
{
    public function testSuccessfulAdminBlocked(): void
    {
        $uuid = Uuid::v4()->__toString();

        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid,
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $this->commandBus->dispatch(new BlockAdminCommand($uuid));

        $status = $this->queryBuilder
            ->select('admin.status')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchOne();

        self::assertEquals('blocked', $status);
    }

    public function testAdminAlreadyBlocked(): void
    {
        $uuid = Uuid::v4()->__toString();

        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid,
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $this->commandBus->dispatch(new BlockAdminCommand($uuid));

        try {
            $this->commandBus->dispatch(new BlockAdminCommand($uuid));
        } catch (DomainException $exception) {
            self::assertEquals("Admin is already blocked.", $exception->getMessage());
        }
    }
}
