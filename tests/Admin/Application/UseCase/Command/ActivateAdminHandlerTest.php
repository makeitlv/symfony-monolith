<?php

declare(strict_types=1);

namespace App\Tests\Admin\Application\UseCase\Command;

use App\Admin\Application\UseCase\Command\Activate\ActivateAdminCommand;
use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Tests\TestCase\ApplicationTestCase;
use Symfony\Component\Uid\Uuid;
use DomainException;

class ActivateAdminHandlerTest extends ApplicationTestCase
{
    public function testSuccessfulAdminActivated(): void
    {
        $uuid = Uuid::v4()->__toString();

        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid,
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $this->commandBus->dispatch(new ActivateAdminCommand($uuid));

        $status = $this->queryBuilder
            ->select('admin.status')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchOne();

        self::assertEquals('activated', $status);
    }

    public function testAdminAlreadyActivated(): void
    {
        $uuid = Uuid::v4()->__toString();

        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid,
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $this->commandBus->dispatch(new ActivateAdminCommand($uuid));

        try {
            $this->commandBus->dispatch(new ActivateAdminCommand($uuid));
        } catch (DomainException $exception) {
            self::assertEquals("Admin is already activated.", $exception->getMessage());
        }
    }
}
