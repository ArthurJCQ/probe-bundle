<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

abstract class AbstractProbeStatusHistory
{
    public ?int $id = null;

    public function __construct(
        public readonly string $probeName,
        public readonly string $probeDescription,
        public ProbeStatus $status,
        public \DateTimeImmutable $checkedAt,
    ) {
    }
}
