<?php

declare(strict_types=1);

namespace App\Tests\Admin\Application\UseCase\Command;

use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Admin\Application\UseCase\Command\NewPassword\NewAdminPasswordCommand;
use App\Tests\TestCase\ApplicationTestCase;
use Symfony\Component\Uid\Uuid;

class NewAdminPasswordHandlerTest extends ApplicationTestCase
{
    public function testSuccessfulAdminPasswordUpdated(): void
    {
        $uuid = Uuid::v4()->__toString();

        $this->commandBus->dispatch(new CreateAdminCommand(
            $uuid,
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $oldPassword = $this->queryBuilder
            ->select('admin.password')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchOne();

        $this->commandBus->dispatch(new NewAdminPasswordCommand($uuid));

        $newPassword = $this->queryBuilder
            ->select('admin.password')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchOne();

        self::assertNotEquals($oldPassword, $newPassword);
    }
}
