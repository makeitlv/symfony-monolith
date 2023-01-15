<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Bus;

use App\Common\Domain\Bus\Query\QueryBusInterface;
use App\Common\Domain\Bus\Query\QueryInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus implements QueryBusInterface
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function handle(QueryInterface $message): mixed
    {
        return $this->handleQuery($message);
    }
}
