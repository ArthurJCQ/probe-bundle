<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Tests\Fixtures;

use Arty\ProbeBundle\Attribute\Probe;
use Arty\ProbeBundle\Model\ProbeInterface;

#[Probe(name: 'failure_probe')]
class FailureProbe implements ProbeInterface
{
    public function check(): int
    {
        return Probe::FAILURE;
    }
}
