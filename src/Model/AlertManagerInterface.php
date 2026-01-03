<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

use Arty\ProbeBundle\Entity\ProbeStatusHistory;

interface AlertManagerInterface
{
    public function sendAlert(ProbeStatusHistory $probeStatusHistory): void;
}
