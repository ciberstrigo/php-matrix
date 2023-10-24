<?php

use Leonidaveryanov\Matrix\Exceptions\KeyIsNotPositiveInteger;
use Leonidaveryanov\Matrix\Matrix;
use Leonidaveryanov\Matrix\Utils\MatrixDumper;
use Leonidaveryanov\Matrix\Vector;
use PHPUnit\Framework\TestCase;

final class MatrixTest extends TestCase
{
    public function testClassConstructor()
    {
        $matrix = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $this->assertSame(1, $matrix->get(0, 0));
        $this->assertSame(5, $matrix->get(1, 1));
        $this->assertSame(9, $matrix->get(2, 2));

        $this->expectException(KeyIsNotPositiveInteger::class);
        $matrix = new Matrix([
            ['impossible_key' => 0]
        ]);
    }

    public function testMatrixMultiply()
    {
        $result = (new Matrix([
            [1, 2, 1],
            [0, 1, 2],
        ]))->productOnMatrix((new Matrix([
            [1, 0],
            [0, 1],
            [1, 1]
        ])));

        $expectedResult = new Matrix([
            [2, 3],
            [2, 3],
        ]);

        $this->assertSame($expectedResult->toArray(), $result->toArray());
    }

    public function testMatrixMultiplyOnScalar()
    {
        $result = (new Matrix([
            [5, -2, 4],
            [3, 1, -3],
        ]))->productOnScalar(7);

        $expectedResult = new Matrix([
            [35, -14, 28],
            [21, 7, -21],
        ]);

        $this->assertSame($expectedResult->toArray(), $result->toArray());
    }

    public function productOnVectorAsColumn()
    {
        $result = (new Matrix([
            [2, 4, 0],
            [-2, 1, 3],
            [-1, 0, 1]
        ]))->productOnVectorAsColumn(new Vector([1, 2, -1]));

        $expectedResult = new Vector([10, -3, -2]);

        $this->assertSame($expectedResult->toArray(), $result->toArray());
    }

    public function testProductVectorAsRow()
    {
        $result = (new Matrix([
            [3],
            [2],
            [0],
            [1]
        ]))->productOnMatrix(new Matrix([[-1, 1, 0, 2]]));

        $expectedResult = new Matrix([
            [-3, 3, 0, 6],
            [-2, 2, 0, 4],
            [0, 0, 0, 0],
            [-1, 1, 0, 2],
        ]);

        $this->assertSame($expectedResult->toArray(), $result->toArray());
    }

    public function testDeterminant()
    {
        $result = (new Matrix([
            [3, -3, -5, 8],
            [-3, 2, 4, -6],
            [2, -5, -7, 5],
            [-4, 3, 5, -6]
        ]))->determinant();

        $this->assertEquals(18, $result);
    }

    public function testMinor()
    {
        $result = (new Matrix([
            [3, -3, -5, 8],
            [-3, 2, 4, -6],
            [2, -5, -7, 5],
            [-4, 3, 5, -6]
        ]))->minor(1, 1);

        $expectedResult = new Matrix([
            [3, -5, 8],
            [2, -7, 5],
            [-4, 5, -6]
        ]);

        $this->assertEquals($expectedResult->toArray(), $result->toArray());
    }

    public function testInverse()
    {
        $result = (new Matrix([
            [2, 5, 7],
            [6, 3, 4],
            [5, -2, -3],
        ]))->inverse();

        $expectedResult = new Matrix([
            [1, -1, 1],
            [-38, 41, -34],
            [27, -29, 24],
        ]);

        $this->assertEquals($expectedResult->toArray(), $result->toArray());
    }

    public function testSubtracting()
    {
        $result = (new Matrix([
            [2, 5, 7],
            [6, 3, 4],
            [5, -2, -3],
        ]))->subtractMatrix(new Matrix([
            [2, 5, 7],
            [6, 3, 4],
            [5, -2, -3]
        ]));

        $expectedResult = new Matrix([
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ]);

        $this->assertEquals($expectedResult->toArray(), $result->toArray());
    }

    public function testAdding()
    {
        $result = (new Matrix([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]))->addMatrix(new Matrix([
            [9, 8, 7],
            [6, 5, 4],
            [3, 2, 1]
        ]));

        $expected = new Matrix([
            [10, 10, 10],
            [10, 10, 10],
            [10, 10, 10]
        ]);

        $this->assertEquals($expected->toArray(), $result->toArray());
    }

    public function testTranspose()
    {
        $result = (new Matrix([
            [2, 1],
            [-3, 0],
            [4, -1]
        ]))->transpose();

        $expected = new Matrix([
            [2, -3, 4],
            [1, 0, -1],
        ]);

        $this->assertEquals($expected->toArray(), $result->toArray());
    }
}