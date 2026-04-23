<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Notifier;

use Arty\ProbeBundle\Model\AbstractProbeStatusHistory;
use Arty\ProbeBundle\Model\AlertManagerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;

final class AlertManager implements AlertManagerInterface
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly string $channel,
        private readonly ?string $to = null,
    ) {
    }

    public function sendAlert(AbstractProbeStatusHistory $probeStatusHistory): void
    {
        $notification = new ProbeNotification($probeStatusHistory, [$this->channel]);

        $recipient = 'email' === $this->channel
            ? new Recipient($this->to ?? '')
            : new NoRecipient();

        $this->notifier->send($notification, $recipient);
    }
}
