<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Mailer;

use Arty\ProbeBundle\Doctrine\Entity\ProbeStatusHistory;
use Arty\ProbeBundle\Model\AlertManagerInterface;
use Arty\ProbeBundle\Model\ProbeFailureEmailInterface;
use Symfony\Component\Mailer\MailerInterface;

final class AlertManager implements AlertManagerInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ProbeFailureEmailInterface $probeFailureEmail,
    ) {
    }

    public function sendAlert(ProbeStatusHistory $probeStatusHistory): void
    {
        $email = $this->probeFailureEmail->createProbeFailureEmail($probeStatusHistory);

        $this->mailer->send($email);
    }
}
