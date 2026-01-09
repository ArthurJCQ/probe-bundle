<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Doctrine\Repository;

use Arty\ProbeBundle\Entity\ProbeStatusHistory;
use Arty\ProbeBundle\Model\AbstractProbeStatusHistory;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<ProbeStatusHistory>
 *
 * @implements ProbeStatusHistoryRepositoryInterface<ProbeStatusHistory>
 */
class ProbeStatusHistoryRepository extends EntityRepository implements ProbeStatusHistoryRepositoryInterface
{
    public function save(AbstractProbeStatusHistory $probeStatusHistory): void
    {
        $this->getEntityManager()->persist($probeStatusHistory);
        $this->getEntityManager()->flush();
    }

    public function findLastByProbeName(string $probeName): ?AbstractProbeStatusHistory
    {
        return $this->createQueryBuilder('psh')
            ->where('psh.probeName = :probeName')
            ->setParameter('probeName', $probeName)
            ->orderBy('psh.checkedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return AbstractProbeStatusHistory[] */
    public function findAllLastStatuses(): array
    {
        $qb = $this->createQueryBuilder('psh');

        $qb->where(
            'psh.checkedAt = (
            SELECT MAX(psh2.checkedAt)
            FROM ' . $this->getEntityName() . ' psh2
            WHERE psh2.probeName = psh.probeName
            )',
        )
            ->orderBy('psh.probeName', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /** @return AbstractProbeStatusHistory[] */
    public function findLast5ByProbeName(string $probeName): array
    {
        return $this->createQueryBuilder('psh')
            ->where('psh.probeName = :probeName')
            ->setParameter('probeName', $probeName)
            ->orderBy('psh.checkedAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
