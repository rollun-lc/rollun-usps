<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Product\Dimensions;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;

class BoxTest extends TestCase
{

    public function testBoxTrue()
    {

        $box = new Box(35, 6, 11);

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);

        $this->assertEquals(
            true, $box->canFit($product)
        );
    }

    public function testBoxFalse()
    {

        $box = new Box(35, 6, 9);

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);

        $this->assertEquals(
            false, $box->canFit($product)
        );
    }

    public function boxWithPackTrueDataProvider()
    {
        return [
            [new Box(35, 20, 10), new Rectangular(10, 10, 5), 2],
            [new Box(20, 20, 20), new Rectangular(5, 5, 5), 4],
        ];
    }

    /**
     * @param Box $box
     * @param Rectangular $productDim
     * @param $qty
     * @dataProvider boxWithPackTrueDataProvider
     */
    public function testBoxWithPackTrue(Box $box, Rectangular $productDim, $qty)
    {
        $productPack = new ProductPack(new Product($productDim, 0.5), $qty);

        $this->assertTrue($box->canFit($productPack));
    }

    public function boxWithPackFalseDataProvider()
    {
        return [
            [new Box(35, 20, 10), new Rectangular(10, 10, 5), 3],

            [new Box(10, 10, 10), new Rectangular(10, 10, 10), 2],
            [new Box(30, 20, 10), new Rectangular(10, 10, 10), 4],
        ];
    }

    /**
     * @param Box $box
     * @param Rectangular $productDim
     * @param $qty
     * @dataProvider boxWithPackFalseDataProvider
     */
    public function testBoxWithPackFalse(Box $box, Rectangular $productDim, $qty)
    {
        $productPack = new ProductPack(new Product($productDim, 0.5), $qty);

        $this->assertFalse($box->canFit($productPack));
    }

}
