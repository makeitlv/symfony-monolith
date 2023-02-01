<?php

declare(strict_types=1);

namespace App\Dashboard\Infrastructure\EventSubscriber;

use App\Dashboard\Settings\DashboardSettings;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Translation\TranslatableMessage;
use RuntimeException;

class FlashMessageSubscriber implements EventSubscriberInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(
        RequestStack $requestStack
    ) {
        $session = $requestStack->getSession();
        if (!$session instanceof FlashBagAwareSessionInterface) {
            throw new RuntimeException('Wrong session!');
        }

        $flashBag = $session->getFlashBag();
        if (!$flashBag instanceof FlashBagInterface) {
            throw new RuntimeException('Wrong flash bag!');
        }

        $this->flashBag = $flashBag;
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
        $this->flashBag->add(
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
        $this->flashBag->add(
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
        $this->flashBag->add(
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
