<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use RuntimeException;

class ValidationFailedException extends RuntimeException
{
    public function __construct(
        private object $violatingObject,
        private ConstraintViolationListInterface $violations
    ) {
        parent::__construct(sprintf('Domain of type "%s" failed validation.', $this->violatingObject::class));
    }

    public function getViolatingObject(): object
    {
        return $this->violatingObject;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
