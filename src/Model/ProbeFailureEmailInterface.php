<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

use Arty\ProbeBundle\Entity\ProbeStatusHistory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

interface ProbeFailureEmailInterface
{
    public function createProbeFailureEmail(ProbeStatusHistory $probeStatusHistory): TemplatedEmail;
}
