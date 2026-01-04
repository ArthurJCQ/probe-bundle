<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Doctrine;

use Arty\ProbeBundle\Doctrine\Repository\ProbeStatusHistoryRepositoryInterface;
use Arty\ProbeBundle\Entity\ProbeStatusHistory;
use Arty\ProbeBundle\Model\ProbeManagerInterface;
use Arty\ProbeBundle\Model\ProbeStatus;
use Doctrine\Persistence\ObjectManager;

class ProbeManager implements ProbeManagerInterface
{
    protected ObjectManager $objectManager;

    /** @var class-string<ProbeStatusHistory> */
    protected string $class;

    /** @var ProbeStatusHistoryRepositoryInterface<ProbeStatusHistory> */
    protected ProbeStatusHistoryRepositoryInterface $repository;

    /**
     * @param class-string<ProbeStatusHistory> $class
     *
     * @throws \LogicException If the object repository does not implement `ProbeStatusHistoryRepositoryInterface`.
     */
    public function __construct(ObjectManager $om, string $class)
    {
        $this->objectManager = $om;

        $repository = $om->getRepository($class);

        if (!$repository instanceof ProbeStatusHistoryRepositoryInterface) {
            throw new \LogicException(sprintf(
                'Repository mapped for "%s" should implement %s.',
                $class,
                ProbeStatusHistoryRepositoryInterface::class,
            ));
        }

        $this->repository = $repository;

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    public function create(
        string $probeName,
        string $probeDescription,
        ProbeStatus $status,
        \DateTimeImmutable $checkedAt,
    ): ProbeStatusHistory {
        return new $this->class(
            $probeName,
            $probeDescription,
            $status,
            $checkedAt,
        );
    }

    public function save(ProbeStatusHistory $probeStatusHistory): void
    {
        $this->objectManager->persist($probeStatusHistory);
        $this->objectManager->flush();
    }

    public function delete(ProbeStatusHistory $probeStatusHistory): void
    {
        $this->objectManager->remove($probeStatusHistory);
        $this->objectManager->flush();
    }

    public function findLastByProbeName(string $probeName): ?ProbeStatusHistory
    {
        return $this->repository->findLastByProbeName($probeName);
    }

    /** @return ProbeStatusHistory[] */
    public function findAllLastStatuses(): array
    {
        return $this->repository->findAllLastStatuses();
    }

    /** @return ProbeStatusHistory[] */
    public function findLast5ByProbeName(string $probeName): array
    {
        return $this->repository->findLast5ByProbeName($probeName);
    }
}
