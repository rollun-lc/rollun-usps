<?php

declare(strict_types=1);

namespace rollun\test\unit\Entity\Product\Dimensions;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;

/**
 * Class BoxTest
 *
 * @author    r.ratsun <r.ratsun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class BoxTest extends TestCase
{
    /**
     * @return array
     */
    public function getCanFitProductPackDataProvider(): array
    {
        return [
            // $box, $pack, $expected
            [new Box(11, 4, 2), new ProductPack(new Product(new Rectangular(2, 2, 2), 0.5), 10), true],
            [new Box(11, 4, 1), new ProductPack(new Product(new Rectangular(1, 3, 3), 0.5), 3), true],
            [new Box(10, 7, 10), new ProductPack(new Product(new Rectangular(2, 2, 7), 0.5), 15), true],
            [new Box(10, 7, 10), new ProductPack(new Product(new Rectangular(2, 6, 6), 0.5), 3), true],
            [new Box(10, 7, 10), new ProductPack(new Product(new Rectangular(8, 8, 8), 0.5), 3), false],
            [new Box(2, 2, 1), new ProductPack(new Product(new Rectangular(3, 2, 1), 0.5), 3), false],
            [new Box(2, 2, 1), new ProductPack(new Product(new Rectangular(2, 2, 1), 0.5), 2), false],
            [new Box(5, 4, 4), new ProductPack(new Product(new Rectangular(1, 1, 1), 0.5), 81), false],
            [new Box(5, 4, 4), new ProductPack(new Product(new Rectangular(1, 2, 2), 0.5), 17), false],
        ];
    }

    /**
     * @param Box         $box
     * @param ProductPack $pack
     * @param bool        $expected
     *
     * @dataProvider getCanFitProductPackDataProvider
     */
    public function testCanFitProductPack(Box $box, ProductPack $pack, bool $expected)
    {
        $this->assertEquals($expected, $box->canFit($pack));
    }

    /**
     * Test for canFit method
     */
    public function testCanFitProductTrue()
    {
        $box = new Box(35, 6, 11);

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);

        $this->assertTrue($box->canFit($product));
    }

    /**
     * Test for canFit method
     */
    public function testCanFitProductFalse()
    {
        $box = new Box(35, 6, 9);

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);

        $this->assertFalse($box->canFit($product));
    }
}
