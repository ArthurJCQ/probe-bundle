<?php

declare(strict_types=1);

namespace Arty\ProbeBundle;

use Arty\ProbeBundle\Model\AlertManagerInterface;
use Arty\ProbeBundle\Model\ProbeInterface;
use Arty\ProbeBundle\Model\ProbeManagerInterface;
use Arty\ProbeBundle\Model\ProbeStatus;
use Arty\ProbeBundle\Model\ProbeStatusHistoryInterface;

readonly final class ProbeRunner
{
    public function __construct(
        /**
         * @var array<string, array{
         *     probeInstance: ProbeInterface,
         *     name: string,
         *     successThreshold: int,
         *     warningThreshold: int,
         *     failureThreshold: int,
         *     description: string,
         *     notify: bool
         * }>
         */
        private array $probesByName,
        private ProbeManagerInterface $probeManager,
        private ?AlertManagerInterface $alertManager,
    ) {
    }

    /** @return ProbeStatusHistoryInterface[] */
    public function runAll(): array
    {
        $results = [];

        foreach (array_keys($this->probesByName) as $name) {
            $results[] = $this->run($name);
        }

        return $results;
    }

    public function run(string $name): ProbeStatusHistoryInterface
    {
        $probeMetadata = $this->probesByName[$name];
        $probe = $probeMetadata['probeInstance'];

        $result = $probe->check();

        $status = match (true) {
            $result >= $probeMetadata['failureThreshold'] => ProbeStatus::FAILED,
            $result >= $probeMetadata['warningThreshold'] => ProbeStatus::WARNING,
            default => ProbeStatus::SUCCESS,
        };

        $probeStatusHistory = $this->probeManager->create(
            $name,
            $probeMetadata['description'],
            $status,
            new \DateTimeImmutable(),
        );

        if (
            $this->alertManager instanceof AlertManagerInterface
            && $status === ProbeStatus::FAILED
            && $this->probeManager->findLastByProbeName($name)?->status !== ProbeStatus::FAILED
            && $probeMetadata['notify']
        ) {
            $this->alertManager->sendAlert($probeStatusHistory);
        }

        $this->probeManager->save($probeStatusHistory);

        return $probeStatusHistory;
    }
}
