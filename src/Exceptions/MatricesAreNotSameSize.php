<?php

namespace Leonidaveryanov\Matrix\Exceptions;

use Throwable;

class MatricesAreNotSameSize extends \LogicException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return sprintf('%s Matrices are not the same size', $this->message);
    }
}