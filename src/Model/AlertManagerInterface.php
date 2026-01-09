<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

interface AlertManagerInterface
{
    public function sendAlert(ProbeStatusHistoryInterface $probeStatusHistory): void;
}
