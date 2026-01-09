<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

interface ProbeFailureEmailInterface
{
    public function createProbeFailureEmail(ProbeStatusHistoryInterface $probeStatusHistory): TemplatedEmail;
}
