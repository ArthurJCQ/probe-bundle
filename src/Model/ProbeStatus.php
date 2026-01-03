<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Model;

enum ProbeStatus: string
{
    case SUCCESS = 'SUCCESS';
    case WARNING = 'WARNING';
    case FAILED = 'FAILED';
}
