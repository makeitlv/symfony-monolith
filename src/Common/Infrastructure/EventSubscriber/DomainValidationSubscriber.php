<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\EventSubscriber;

use App\Common\Domain\Entity\Aggregate;
use App\Common\Infrastructure\Exception\ValidationFailedException;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use RuntimeException;

class DomainValidationSubscriber implements EventSubscriber
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!($entity instanceof Aggregate)) {
            return;
        }

        /** @var ClassMetadata $metadata */
        $metadata = $this->validator->getMetadataFor($entity);
        if (!count($metadata->getConstrainedProperties())) {
            throw new RuntimeException(sprintf('Validation rules is not set for class %s', $entity::class));
        }

        $violations = $this->validator->validate($entity);
        if (count($violations)) {
            throw new ValidationFailedException($entity, $violations);
        }
    }
}
