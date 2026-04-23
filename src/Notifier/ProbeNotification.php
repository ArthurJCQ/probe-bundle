<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Notifier;

use Arty\ProbeBundle\Model\AbstractProbeStatusHistory;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

final class ProbeNotification extends Notification implements ChatNotificationInterface
{
    public function __construct(AbstractProbeStatusHistory $probeStatusHistory, array $channels = [])
    {
        parent::__construct(
            subject: sprintf('Probe Failure: %s', $probeStatusHistory->probeName),
            channels: $channels,
        );

        $this->content(sprintf(
            'The probe "%s" has failed at %s. Please check the system as soon as possible.',
            $probeStatusHistory->probeName,
            $probeStatusHistory->checkedAt->format('Y-m-d H:i:s'),
        ));

        $this->importance(self::IMPORTANCE_URGENT);
    }

    public function asChatMessage(RecipientInterface $recipient, ?string $transport = null): ?ChatMessage
    {
        return ChatMessage::fromNotification($this);
    }
}
