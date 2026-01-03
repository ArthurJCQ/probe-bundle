<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Doctrine\Entity;

use Arty\ProbeBundle\Model\ProbeStatus;

abstract class ProbeStatusHistory
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
