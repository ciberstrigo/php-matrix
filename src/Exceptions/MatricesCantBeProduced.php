<?php declare(strict_types=1);

namespace Leonidaveryanov\Matrix\Exceptions;

use Throwable;

class MatricesCantBeProduced extends \LogicException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return sprintf('%s Matrices can\'t. be produced', $this->message);
    }
}