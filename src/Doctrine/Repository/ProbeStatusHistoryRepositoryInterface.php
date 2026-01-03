<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Doctrine\Repository;

use Arty\ProbeBundle\Doctrine\Entity\ProbeStatusHistory;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of ProbeStatusHistory
 *
 * @extends ObjectRepository<T>
 */
interface ProbeStatusHistoryRepositoryInterface extends ObjectRepository
{
    public function save(ProbeStatusHistory $probeStatusHistory): void;

    public function findLastByProbeName(string $probeName): ?ProbeStatusHistory;

    /** @return ProbeStatusHistory[] */
    public function findAllLastStatuses(): array;

    /** @return ProbeStatusHistory[] */
    public function findLast5ByProbeName(string $probeName): array;
}
