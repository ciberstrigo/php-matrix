<?php

use Leonidaveryanov\Matrix\Matrix;
use Leonidaveryanov\Matrix\Utils\MatrixDumper;
use PHPUnit\Framework\TestCase;

final class EachTest extends TestCase
{
    public function testEachOnCloningObjets()
    {
        $matrix = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $clone = $matrix->each(fn($e) => 0);

        $this->assertNotSame($matrix->toArray(), $clone->toArray());
        $this->assertEquals(0, $clone->get(1, 1));
        $this->assertNotEquals(0, $matrix->get(1, 1));
    }
}