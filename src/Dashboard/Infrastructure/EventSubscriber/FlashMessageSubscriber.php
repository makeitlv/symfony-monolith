<?php

declare(strict_types=1);

namespace App\Dashboard\Infrastructure\EventSubscriber;

use App\Dashboard\Settings\DashboardSettings;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatableMessage;

class FlashMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'flashMessageAfterPersist',
            AfterEntityUpdatedEvent::class => 'flashMessageAfterUpdate',
            AfterEntityDeletedEvent::class => 'flashMessageAfterDelete'
        ];
    }

    public function flashMessageAfterPersist(AfterEntityPersistedEvent $event): void
    {
        $this->requestStack->getSession()->getFlashBag()->add(
            'success',
            new TranslatableMessage(
                'Content "%name%" has been created!',
                [
                    '%name%' => (string) $event->getEntityInstance()
                ],
                DashboardSettings::ADMIN_TRANSLATION_DOMAIN
            )
        );
    }

    public function flashMessageAfterUpdate(AfterEntityUpdatedEvent $event): void
    {
        $this->requestStack->getSession()->getFlashBag()->add(
            'success',
            new TranslatableMessage(
                'Content "%name%" has been updated!',
                [
                    '%name%' => (string) $event->getEntityInstance()
                ],
                DashboardSettings::ADMIN_TRANSLATION_DOMAIN
            )
        );
    }

    public function flashMessageAfterDelete(AfterEntityDeletedEvent $event): void
    {
        $this->requestStack->getSession()->getFlashBag()->add(
            'success',
            new TranslatableMessage(
                'Content "%name%" has been deleted!',
                [
                    '%name%' => (string) $event->getEntityInstance()
                ],
                DashboardSettings::ADMIN_TRANSLATION_DOMAIN
            )
        );
    }
}
