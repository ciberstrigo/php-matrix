<?php declare(strict_types=1);

namespace Leonidaveryanov\Matrix\Exceptions;

use Throwable;

class IsNotSquareMatrix extends \LogicException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return sprintf('%s Matrix must be a square.', $this->message);
    }
}