<?php declare(strict_types=1);

namespace Leonidaveryanov\Matrix;

use Leonidaveryanov\Matrix\Exceptions\MatricesAreNotSameSize;
use Leonidaveryanov\Matrix\Utils\MatrixDumper;
use LogicException;


/**
 * @Matrix
 */
class Matrix
{
    use PositiveIntegerKeys;

    /**
     * @var array
     */
    private array $container;

    /**
     * @var mixed
     */
    private mixed $cachedDeterminant = null;

    /**
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->checkArrayKeysOrThrowException($array);
        $this->container = $array;
    }

    public function __clone()
    {
        $this->resetCached();
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return count($this->container);
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        if (array_key_exists(0, $this->container) && is_array($this->container[0])) {
            return count($this->container[0]);
        }

        throw new LogicException('Impossible to get height of matrix');
    }

    /**
     * Return the result of adding matrices. (+)
     * @param Matrix $matrix
     * @return Matrix
     */
    public function addMatrix(Matrix $matrix): Matrix
    {
        if (!$this->sameSize($matrix)) {
            throw new MatricesAreNotSameSize('Matrices can\'t be added.');
        }

        return $this->each(fn($e, $x, $y) => $this->get($x, $y) + $matrix->get($x, $y));
    }

    /**
     * Return the result of Matrices subtracting (-)
     * @param Matrix $matrix
     * @return Matrix
     */
    public function subtractMatrix(Matrix $matrix): Matrix
    {
        if (!$this->sameSize($matrix)) {
            throw new MatricesAreNotSameSize('Can\'t subtract matrices.');
        }

        return $this->each(fn($e, $x, $y) => $this->get($x, $y) - $matrix->get($x, $y));
    }

    /**
     * Return new transposed matrix
     * @return Matrix
     * @link https://www.webmath.ru/poleznoe/formules_6_7.php
     */
    public function transpose(): Matrix
    {
        $res = new Matrix();

        for ($x = 0; $x < $this->getWidth(); $x++) {
            for ($y = 0; $y < $this->getHeight(); $y++) {
                $res->set($y, $x, $this->get($x, $y));
            }
        }

        return $res;
    }

    /**
     * Calculating determinant
     * @return mixed
     * @link http://mathprofi.ru/kak_vychislit_opredelitel.html
     */
    public function determinant(): mixed
    {
        if ($this->cachedDeterminant) {
            return $this->cachedDeterminant;
        }

        if (!$this->isSquare()) {
            throw new LogicException('To calculate determinant, matrix must be squared');
        }

        if (1 === $this->getWidth() && 1 === $this->getHeight()) {
            return $this->get(0, 0);
        }

        if (2 === $this->getWidth() && 2 === $this->getHeight()) {
            return $this->get(0, 0) * $this->get(1, 1)
                - $this->get(0, 1) * $this->get(1, 0);
        }

        $det = 0;
        for ($y = 0; $y < $this->getHeight(); $y++) {
            $subMatrix = $this->minor(0, $y);
            $sign = ($y % 2 == 0) ? 1 : -1;
            $det += $sign * $this->get(0, $y) * $subMatrix->determinant();
        }

        return $this->cachedDeterminant = $det;
    }

    /**
     * Return new inverted matrix
     * @return Matrix
     * @link http://mathprofi.ru/kak_naiti_obratnuyu_matricu.html
     */
    public function inverse(): Matrix
    {
        if (false === $this->isSquare()) {
            throw new \LogicException('Matrix must be a square');
        }

        if (0 === $determinant = $this->determinant()) {
            throw new \LogicException('Cant invert matrix with zero determinant');
        }

        $transposed = $this->cofactorMatrix()->transpose();

        return $transposed->productOnScalar(1 / $determinant);
    }

    /**
     * Return the minor of matrix.
     * @param $x
     * @param $y
     * @return Matrix
     * @link https://miemp-mi-gor.narod.ru/utcheba/matem/matrica/004.htm
     */
    public function minor($x, $y): Matrix
    {
        $minor = new Matrix();
        $xCounter = 0;
        for ($x2 = 0; $x2 < $this->getWidth(); $x2++) {
            $yCounter = 0;
            if ($x2 !== $x) {
                for ($y2 = 0; $y2 < $this->getHeight(); $y2++) {
                    if ($y2 !== $y) {
                        $minor->set($xCounter, $yCounter, $this->get($x2, $y2));
                        $yCounter++;
                    }
                }
                $xCounter++;
            }
        }

        return $minor;
    }

    /**
     * Return the matrix of determinant of mirrors for each cell.
     * @return Matrix
     */
    public function minorMatrix(): Matrix
    {
        $result = new Matrix();
        for ($x = 0; $x < $this->getWidth(); $x++) {
            for ($y = 0; $y < $this->getHeight(); $y++) {
                $result->set($x, $y, $this->minor($x, $y)->determinant());
            }
        }

        return $result;
    }


    /**
     * @return Matrix
     */
    public function cofactorMatrix(): Matrix
    {
        $res = $this->minorMatrix();
        for ($x = 0; $x < $this->getWidth(); $x++) {
            for ($y = 0; $y < $this->getHeight(); $y++) {
                $sign = (($y+$x) % 2 == 0) ? 1 : -1;
                $res->set($x, $y, $res->get($x, $y) * $sign);
            }
        }

        return $res;
    }

    /**
     * @param Matrix $matrix
     * @return Matrix
     */
    public function productOnMatrix(Matrix $matrix): Matrix
    {
        if (!count($this->container) || !$matrix->getWidth()) {
            throw new \LogicException('Matrix cant be produced');
        }

        if (count($this->container[0]) !== $matrix->getWidth()) {
            throw new \LogicException('Matrix cant be produced');
        }

        $res = new Matrix();

        for ($x = 0; $x < $this->getWidth(); $x++) {
            for ($y = 0; $y < $this->getWidth(); $y++) {
                $sum = 0;
                for ($i = 0; $i < $this->getHeight(); $i++) {
                    $sum += $this->get($x, $i) * $matrix->get($i, $y);
                }
                $res->set($x, $y, $sum);
            }
        }

        return $res;
    }

    /**
     * @param int|float $num
     * @return Matrix
     */
    public function productOnScalar(int|float $num): Matrix
    {
        $res = new Matrix();
        for ($x = 0; $x < $this->getWidth(); $x++) {
            for ($y = 0; $y < $this->getHeight(); $y++) {
                $res->set($x, $y, $this->get($x, $y) * $num);
            }
        }

        return $res;
    }

    /**
     * @param Vector $vector
     * @return Vector
     */
    public function productOnVectorAsColumn(Vector $vector): Vector
    {
        $res = new Vector();
        for ($x = 0; $x < count($this->container); $x++) {
            for ($y = 0; $y < count($this->container[$x]); $y++) {
                if (!isset($res[$x])) {
                    $res[$x] = 0;
                }
                $res[$x] += $this->container[$x][$y] * $vector[$y];
            }
        }

        return $res;
    }

    /**
     * @param Vector $vector
     * @return Matrix
     */
    public function productOnVectorAsRow(Vector $vector): Matrix
    {
        if ($this->getHeight() !== $vector->count()) {
            throw new \LogicException('Height of matrix should be equal to vector length');
        }

        if ($this->getWidth() > 1) {
            throw new \LogicException('Matrix Width should be equal to 1');
        }

        return $this->productOnMatrix($vector->toRowMatrix());
    }

    /**
     * @param Matrix $matrix
     * @return bool
     */
    public function same(Matrix $matrix): bool
    {
        return $this->toArray() === $matrix->toArray();
    }

    /**
     * @param Matrix $matrix
     * @return bool
     */
    public function sameSize(Matrix $matrix): bool
    {
        return $this->getWidth() === $matrix->getWidth()
            && $this->getHeight() === $matrix->getHeight();
    }

    /**
     * @param int $x
     * @param int $y
     * @return mixed
     */
    public function get(int $x, int $y): mixed
    {
        return $this->container[$x][$y];
    }

    /**
     * @param int $x
     * @return array
     */
    public function getRow(int $x): array
    {
        return $this->container[$x];
    }

    /**
     * @param int $y
     * @return array
     */
    public function getColumn(int $y): array
    {
        return array_map(fn ($row) => $row[$y], $this->container);
    }

    /**
     * Set value to matrix by coordinates.
     * Despite other methods, this one will not produce a new Matrix object. It'll return the same Matrix.
     * @param int $x
     * @param int $y
     * @param mixed $value
     * @return $this
     */
    public function set(int $x, int $y, mixed $value): self
    {
        if (!array_key_exists($x, $this->container)) {
            $this->container[$x] = [];
        }

        $this->container[$x][$y] = $value;
        $this->resetCached();
        return $this;
    }

    /**
     * This method creating a new Matrix by modification current Matrix though callback function.
     * Callback function have to receive element and xy coordinates as a parameters.
     * For example: `fn($element, $x, $y) => 0`
     * @param \Closure $fn
     * @return Matrix
     */
    public function each(\Closure $fn): Matrix
    {
        $forEach = new Matrix();

        for ($x = 0; $x < $this->getWidth(); $x++) {
            for ($y = 0; $y < $this->getHeight(); $y++) {
                $forEach->container[$x][$y] = $fn($this->get($x, $y), $x, $y);
            }
        }

        return $forEach;
    }

    /**
     * @param int $x
     * @param mixed $value
     * @return Matrix
     */
    public function insertIntoRow(int $x, mixed $value): Matrix
    {
        $clone = clone $this;
        $clone->container[$x][] = $value;
        return $clone;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->container;
    }

    /**
     * @return bool
     */
    public function isSquare(): bool
    {
        return $this->getWidth() === $this->getHeight();
    }

    private function resetCached(): void
    {
        $this->cachedDeterminant = null;
    }
}