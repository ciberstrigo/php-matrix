<?php declare(strict_types=1);

namespace Leonidaveryanov\Matrix\Utils;

use Leonidaveryanov\Matrix\Matrix;

class MatrixDumper
{
    public static function dump(Matrix $matrix): void
    {
        echo \sprintf('Object id: %s.%s', spl_object_id($matrix), PHP_EOL);
        echo \sprintf(
            'Size: %sx%s. Is square?: %s%s',
            $matrix->getWidth(),
            $matrix->getHeight(),
            $matrix->isSquare() ? 'true' : 'false',
            PHP_EOL
        );
        if ($matrix->isSquare()) {
            echo \sprintf('Determinant: %s%s', $matrix->determinant(), PHP_EOL);
        }

        for ($x = 0; $x < $matrix->getWidth(); $x++) {
            echo('[ '.implode(' , ', $matrix->getRow($x)).' ]'.PHP_EOL);
        }
        echo PHP_EOL;
    }
}