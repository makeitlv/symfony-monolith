<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Bus;

use App\Common\Domain\Bus\Event\EventBusInterface;
use App\Common\Domain\Bus\Event\EventInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class EventBus implements EventBusInterface
{
    public function __construct(
        private MessageBusInterface $eventBus
    ) {
    }

    public function publish(EventInterface $event): void
    {
        try {
            $this->eventBus->dispatch($event);
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                /** @var Throwable $e */
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
