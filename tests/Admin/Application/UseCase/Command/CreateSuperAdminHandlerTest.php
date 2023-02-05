<?php

declare(strict_types=1);

namespace App\Tests\Admin\Application\UseCase\Command;

use App\Admin\Application\UseCase\Command\CreateSuper\CreateSuperAdminCommand;
use App\Client\Domain\Entity\ValueObject\Status;
use App\Tests\TestCase\ApplicationTestCase;
use Symfony\Component\Uid\Uuid;

class CreateSuperAdminHandlerTest extends ApplicationTestCase
{
    public function testSuccessfulSuperAdminCreated(): void
    {
        $this->commandBus->dispatch(new CreateSuperAdminCommand(
            $uuid = Uuid::v4()->__toString(),
            'admin@admin.com',
            'Admin',
            'Admin',
            'pswrd'
        ));

        $admin = $this->queryBuilder
            ->select('*')
            ->from('admin')
            ->where('admin.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->fetchAssociative();

        self::assertNotEmpty($admin);
        self::assertEquals('activated', $admin['status']);
        self::assertNull($admin['confirmation_token']);
    }
}
