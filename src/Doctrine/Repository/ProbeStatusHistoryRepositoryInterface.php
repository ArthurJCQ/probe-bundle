<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Doctrine\Repository;

use Arty\ProbeBundle\Model\ProbeStatusHistoryInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of ProbeStatusHistoryInterface
 *
 * @extends ObjectRepository<T>
 */
interface ProbeStatusHistoryRepositoryInterface extends ObjectRepository
{
    public function save(ProbeStatusHistoryInterface $probeStatusHistory): void;

    public function findLastByProbeName(string $probeName): ?ProbeStatusHistoryInterface;

    /** @return ProbeStatusHistoryInterface[] */
    public function findAllLastStatuses(): array;

    /** @return ProbeStatusHistoryInterface[] */
    public function findLast5ByProbeName(string $probeName): array;
}
