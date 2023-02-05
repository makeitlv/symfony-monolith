<?php

declare(strict_types=1);

namespace App\Tests\Admin\Application\UseCase\Command;

use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Tests\TestCase\ApplicationTestCase;
use Symfony\Component\Uid\Uuid;
use DomainException;

class CreateAdminHandlerTest extends ApplicationTestCase
{
    public function testSuccessfulAdminCreated(): void
    {
        $this->commandBus->dispatch(new CreateAdminCommand(
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
        self::assertEquals('disabled', $admin['status']);
        self::assertNotNull($admin['confirmation_token']);
    }

    public function testSuperAdminAlreadyExists(): void
    {
        $email = 'admin@admin.com';

        $this->commandBus->dispatch(new CreateAdminCommand(
            Uuid::v4()->__toString(),
            $email,
            'Admin',
            'Admin',
            'pswrd'
        ));

        try {
            $this->commandBus->dispatch(new CreateAdminCommand(
                Uuid::v4()->__toString(),
                $email,
                'Admin',
                'Admin',
                'pswrd'
            ));
        } catch (DomainException $exception) {
            self::assertEquals(
                sprintf('Admin already exists with such email %s.', $email),
                $exception->getMessage()
            );
        }
    }
}
