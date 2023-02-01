<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Bus;

use App\Common\Domain\Bus\Command\CommandBusInterface;
use App\Common\Domain\Bus\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class CommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus
    ) {
    }

    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                /** @var Throwable $e */
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
