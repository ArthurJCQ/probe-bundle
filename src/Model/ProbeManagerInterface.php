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
    ): ProbeStatusHistoryInterface;

    public function save(ProbeStatusHistoryInterface $probeStatusHistory): void;

    public function delete(ProbeStatusHistoryInterface $probeStatusHistory): void;

    public function findLastByProbeName(string $probeName): ?ProbeStatusHistoryInterface;

    /** @return ProbeStatusHistoryInterface[] */
    public function findAllLastStatuses(): array;

    /** @return ProbeStatusHistoryInterface[] */
    public function findLast5ByProbeName(string $probeName): array;
}
