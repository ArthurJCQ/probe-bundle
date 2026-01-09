<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Doctrine\Repository;

use Arty\ProbeBundle\Model\AbstractProbeStatusHistory;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of AbstractProbeStatusHistory
 *
 * @extends ObjectRepository<T>
 */
interface ProbeStatusHistoryRepositoryInterface extends ObjectRepository
{
    public function save(AbstractProbeStatusHistory $probeStatusHistory): void;

    public function findLastByProbeName(string $probeName): ?AbstractProbeStatusHistory;

    /** @return AbstractProbeStatusHistory[] */
    public function findAllLastStatuses(): array;

    /** @return AbstractProbeStatusHistory[] */
    public function findLast5ByProbeName(string $probeName): array;
}
