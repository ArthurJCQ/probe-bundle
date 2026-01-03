<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

interface ProbeInterface
{
    public function check(): int;
}
