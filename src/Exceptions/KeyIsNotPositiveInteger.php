<?php

namespace Leonidaveryanov\Matrix\Exceptions;

class KeyIsNotPositiveInteger extends \LogicException
{
    public function __toString(): string
    {
        return 'Position of value must be integer, 0 or higher';
    }
}