<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Probe
{
    public const int SUCCESS = 0;
    public const int WARNING = 1;
    public const int FAILURE = 2;

    public function __construct(
        public string $name,
        public int $successThreshold = self::SUCCESS,
        public int $warningThreshold = self::WARNING,
        public int $failureThreshold = self::FAILURE,
        public bool $notify = true,
        public ?string $description = null,
    ) {
    }
}
