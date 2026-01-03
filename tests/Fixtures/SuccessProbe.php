<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Tests\Fixtures;

use Arty\ProbeBundle\Attribute\Probe;
use Arty\ProbeBundle\Model\ProbeInterface;

#[Probe(name: 'success_probe')]
class SuccessProbe implements ProbeInterface
{
    public function check(): int
    {
        return Probe::SUCCESS;
    }
}
