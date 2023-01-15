<?php

namespace App\Common\Infrastructure\Bus;

use App\Common\Domain\Bus\Event\EventBusInterface;
use App\Common\Domain\Bus\Event\EventInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBus implements EventBusInterface
{
    public function __construct(
        private MessageBusInterface $eventBus
    ) {
    }

    public function publish(EventInterface $event): void
    {
        $this->eventBus->dispatch($event);
    }
}
