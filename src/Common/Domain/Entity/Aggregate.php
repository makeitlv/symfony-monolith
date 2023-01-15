<?php

declare(strict_types=1);

namespace App\Common\Domain\Entity;

use App\Common\Domain\Bus\Event\EventInterface;

abstract class Aggregate
{
    /**
     * @var EventInterface[]
     */
    private array $events = [];

    /**
     * @return EventInterface[]
     */
    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    protected function raise(EventInterface $event): void
    {
        $this->events[] = $event;
    }
}
