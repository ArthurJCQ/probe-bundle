<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Mailer;

use Arty\ProbeBundle\Model\ProbeFailureEmailInterface;
use Arty\ProbeBundle\Model\ProbeStatusHistoryInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

readonly class ProbeFailureEmail implements ProbeFailureEmailInterface
{
    public function __construct(
        private ?string $fromAddress,
        private ?string $fromName,
        private ?string $to,
        private ?string $subject,
        private ?string $template,
    ) {
    }

    public function createProbeFailureEmail(ProbeStatusHistoryInterface $probeStatusHistory): TemplatedEmail
    {
        if (!$this->fromAddress || !$this->to || !$this->subject || !$this->template) {
            throw new \RuntimeException('Required parameters are missing in ArtyProbeBundle alerting configuration.');
        }

        return (new TemplatedEmail())
            ->from(new Address($this->fromAddress, $this->fromName ?? ''))
            ->to($this->to)
            ->subject($this->subject)
            ->htmlTemplate($this->template)
            ->context([
                'name' => $probeStatusHistory->probeName,
                'checkedAt' => $probeStatusHistory->checkedAt,
            ]);
    }
}
