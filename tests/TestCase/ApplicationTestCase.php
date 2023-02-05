<?php

declare(strict_types=1);

namespace App\Tests\TestCase;

use App\Common\Domain\Bus\Command\CommandBusInterface;
use App\Common\Domain\Bus\Query\QueryBusInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ApplicationTestCase extends KernelTestCase
{
    protected ?CommandBusInterface $commandBus;
    protected ?QueryBusInterface $queryBus;
    protected ?EntityManagerInterface $entityManager;
    protected ?QueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
        $this->queryBus = self::getContainer()->get(QueryBusInterface::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->commandBus = null;
        $this->queryBus = null;
        $this->entityManager = null;
        $this->queryBuilder = null;
    }
}
