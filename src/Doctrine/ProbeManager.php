<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Doctrine;

use Arty\ProbeBundle\Doctrine\Repository\ProbeStatusHistoryRepositoryInterface;
use Arty\ProbeBundle\Model\ProbeManagerInterface;
use Arty\ProbeBundle\Model\ProbeStatus;
use Arty\ProbeBundle\Model\ProbeStatusHistoryInterface;
use Doctrine\Persistence\ObjectManager;

class ProbeManager implements ProbeManagerInterface
{
    protected ObjectManager $objectManager;

    /** @var class-string<ProbeStatusHistoryInterface> */
    protected string $class;

    /** @var ProbeStatusHistoryRepositoryInterface<ProbeStatusHistoryInterface> */
    protected ProbeStatusHistoryRepositoryInterface $repository;

    /**
     * @param class-string<ProbeStatusHistoryInterface> $class
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
    ): ProbeStatusHistoryInterface {
        return new $this->class(
            $probeName,
            $probeDescription,
            $status,
            $checkedAt,
        );
    }

    public function save(ProbeStatusHistoryInterface $probeStatusHistory): void
    {
        $this->objectManager->persist($probeStatusHistory);
        $this->objectManager->flush();
    }

    public function delete(ProbeStatusHistoryInterface $probeStatusHistory): void
    {
        $this->objectManager->remove($probeStatusHistory);
        $this->objectManager->flush();
    }

    public function findLastByProbeName(string $probeName): ?ProbeStatusHistoryInterface
    {
        return $this->repository->findLastByProbeName($probeName);
    }

    /** @return ProbeStatusHistoryInterface[] */
    public function findAllLastStatuses(): array
    {
        return $this->repository->findAllLastStatuses();
    }

    /** @return ProbeStatusHistoryInterface[] */
    public function findLast5ByProbeName(string $probeName): array
    {
        return $this->repository->findLast5ByProbeName($probeName);
    }
}
