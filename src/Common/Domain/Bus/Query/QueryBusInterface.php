<?php

declare(strict_types=1);

namespace App\Common\Domain\Bus\Query;

interface QueryBusInterface
{
    public function handle(QueryInterface $message): mixed;
}
