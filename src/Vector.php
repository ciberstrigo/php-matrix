<?php declare(strict_types=1);

namespace Leonidaveryanov\Matrix;

use ArrayAccess;
use Countable;

class Vector implements ArrayAccess, Countable
{
    use PositiveIntegerKeys;
    private array $container;

    public function __construct(array $array = [])
    {
        $this->checkArrayKeysOrThrowException($array);
        $this->container = $array;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->set($offset, $value);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }

    public function count(): int
    {
        return count($this->container);
    }

    public function get(int $x): mixed
    {
        return $this->container[$x];
    }

    public function set(int $x, mixed $value): self
    {
        $this->checkKeyOrThrowException($x);
        $this->container[$x] = $value;
        return $this;
    }

    public function toArray(): array
    {
        return $this->container;
    }

    public function toRowMatrix(): Matrix
    {
        return new Matrix([$this->toArray()]);
    }

    public function toColumnMatrix(): Matrix
    {
        return new Matrix(array_map(fn($e) => [$e], $this->container));
    }
}