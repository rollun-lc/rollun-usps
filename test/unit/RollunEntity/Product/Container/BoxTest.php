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
    public function getProductPackDataProvider(): array
    {
        return [
            // $box, $pack, $packDimensions
            [new Box(35, 40, 50), new ProductPack(new Product(new Rectangular(10, 30, 5), 0.5), 7), ['max' => 30, 'mid' => 30, 'min' => 25]],
            [new Box(3, 2, 2), new ProductPack(new Product(new Rectangular(1, 1, 2), 0.5), 3), ['max' => 2, 'mid' => 2, 'min' => 2]],
            [new Box(5, 11, 20), new ProductPack(new Product(new Rectangular(10, 1, 20), 0.5), 5), ['max' => 20, 'mid' => 10, 'min' => 5]],
        ];
    }

    /**
     * @param Box         $box
     * @param ProductPack $pack
     * @param array       $packDimensions
     *
     * @dataProvider getProductPackDataProvider
     */
    public function testGetPackDimensions(Box $box, ProductPack $pack, array $packDimensions)
    {
        $this->assertEquals($packDimensions, $box->getPackDimensions($pack));
    }

    /**
     * @param Box         $box
     * @param ProductPack $pack
     *
     * @dataProvider getProductPackDataProvider
     */
    public function testCanFitProductPack(Box $box, ProductPack $pack)
    {
        $this->assertTrue($box->canFit($pack));
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

    /**
     * @return array
     */
    public function boxWithPackTrueDataProvider()
    {
        return [
            [new Box(35, 20, 10), new Rectangular(10, 10, 5), 2],
            [new Box(20, 20, 20), new Rectangular(5, 5, 5), 4],
        ];
    }

    /**
     * @param Box         $box
     * @param Rectangular $productDim
     * @param int         $qty
     *
     * @dataProvider boxWithPackTrueDataProvider
     */
    public function testBoxWithPackTrue(Box $box, Rectangular $productDim, int $qty)
    {
        $productPack = new ProductPack(new Product($productDim, 0.5), $qty);

        $this->assertTrue($box->canFit($productPack));
    }

    /**
     * @return array
     */
    public function boxWithPackFalseDataProvider(): array
    {
        return [
            [new Box(35, 20, 10), new Rectangular(10, 10, 5), 5],
            [new Box(10, 10, 10), new Rectangular(10, 10, 10), 2],
            [new Box(30, 20, 10), new Rectangular(10, 10, 10), 4],
        ];
    }

    /**
     * @param Box         $box
     * @param Rectangular $productDim
     * @param int         $qty
     *
     * @dataProvider boxWithPackFalseDataProvider
     */
    public function testBoxWithPackFalse(Box $box, Rectangular $productDim, int $qty)
    {
        $productPack = new ProductPack(new Product($productDim, 0.5), $qty);

        $this->assertFalse($box->canFit($productPack));
    }
}
