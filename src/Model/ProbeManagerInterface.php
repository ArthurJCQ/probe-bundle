<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

interface ProbeManagerInterface
{
    public function create(
        string $probeName,
        string $probeDescription,
        ProbeStatus $status,
        \DateTimeImmutable $checkedAt,
    ): AbstractProbeStatusHistory;

    public function save(AbstractProbeStatusHistory $probeStatusHistory): void;

    public function delete(AbstractProbeStatusHistory $probeStatusHistory): void;

    public function findLastByProbeName(string $probeName): ?AbstractProbeStatusHistory;

    /** @return AbstractProbeStatusHistory[] */
    public function findAllLastStatuses(): array;

    /** @return AbstractProbeStatusHistory[] */
    public function findLast5ByProbeName(string $probeName): array;
}
