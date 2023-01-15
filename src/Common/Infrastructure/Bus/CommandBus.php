<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Bus;

use App\Common\Domain\Bus\Command\CommandBusInterface;
use App\Common\Domain\Bus\Command\CommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus
    ) {
    }

    public function dispatch(CommandInterface $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
