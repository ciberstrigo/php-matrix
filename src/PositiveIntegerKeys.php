<?php declare(strict_types=1);
namespace Leonidaveryanov\Matrix;

use Leonidaveryanov\Matrix\Exceptions\KeyIsNotPositiveInteger;

trait PositiveIntegerKeys
{
    private function checkKeyOrThrowException(mixed $key): void
    {
        if (!is_int($key) || $key < 0) {
            throw new KeyIsNotPositiveInteger();
        }
    }

    private function checkArrayKeysOrThrowException(array $array): void
    {
        foreach ($array as $key => $value) {
            $this->checkKeyOrThrowException($key);
            if (is_array($value)) {
                $this->checkArrayKeysOrThrowException($value);
            }
        }
    }
}