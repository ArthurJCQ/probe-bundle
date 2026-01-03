<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

use Arty\ProbeBundle\Doctrine\Entity\ProbeStatusHistory;

interface ProbeManagerInterface
{
    public function create(
        string $probeName,
        string $probeDescription,
        ProbeStatus $status,
        \DateTimeImmutable $checkedAt,
    ): ProbeStatusHistory;

    public function save(ProbeStatusHistory $probeStatusHistory): void;

    public function delete(ProbeStatusHistory $probeStatusHistory): void;

    public function findLastByProbeName(string $probeName): ?ProbeStatusHistory;

    /** @return ProbeStatusHistory[] */
    public function findAllLastStatuses(): array;

    /** @return ProbeStatusHistory[] */
    public function findLast5ByProbeName(string $probeName): array;
}
