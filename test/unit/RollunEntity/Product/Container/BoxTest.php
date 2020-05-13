<?php

declare(strict_types=1);

namespace rollun\test\unit\Entity\Product\Dimensions;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductKit;
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
    public function getCanFitDataProvider(): array
    {
        return [
            // $box, $item, $expected
            [new Box(13, 9, 2), new Product(new Rectangular(8, 9, 2), 0.5), true],
            [new Box(2, 2, 2), new Product(new Rectangular(2, 2, 2), 0.5), true],
            [new Box(2, 2, 2), new Product(new Rectangular(2, 2, 1), 0.5), true],
            [new Box(13, 9, 2), new Product(new Rectangular(8, 9, 6), 0.5), false],
            [new Box(2, 2, 2), new Product(new Rectangular(2, 2, 3), 0.5), false],
            [new Box(13, 9, 2), new ProductPack(new Product(new Rectangular(8, 2, 2), 0.5), 6), true],
            [new Box(13, 9, 2), new ProductPack(new Product(new Rectangular(12, 5, 1), 0.5), 1), true],
            [new Box(11, 4, 2), new ProductPack(new Product(new Rectangular(2, 2, 2), 0.5), 10), true],
            [new Box(11, 4, 1), new ProductPack(new Product(new Rectangular(1, 3, 3), 0.5), 3), true],
            [new Box(10, 7, 10), new ProductPack(new Product(new Rectangular(2, 2, 7), 0.5), 15), true],
            [new Box(10, 7, 10), new ProductPack(new Product(new Rectangular(2, 6, 6), 0.5), 3), true],
            [new Box(10, 7, 10), new ProductPack(new Product(new Rectangular(8, 8, 8), 0.5), 3), false],
            [new Box(2, 2, 1), new ProductPack(new Product(new Rectangular(3, 2, 1), 0.5), 3), false],
            [new Box(2, 2, 1), new ProductPack(new Product(new Rectangular(2, 2, 1), 0.5), 2), false],
            [new Box(5, 4, 4), new ProductPack(new Product(new Rectangular(1, 1, 1), 0.5), 81), false],
            [new Box(5, 4, 4), new ProductPack(new Product(new Rectangular(1, 2, 2), 0.5), 21), false],
            [new Box(7, 8, 10), new ProductKit(
                [
                    new ProductPack(new Product(new Rectangular(3, 5, 7), 0.5), 3),
                    new Product(new Rectangular(2, 2, 2), 0.5),
                    new ProductPack(new Product(new Rectangular(2, 3, 9), 0.5), 2),
                ]
            ), true],
            [new Box(7, 8, 10), new ProductKit(
                [
                    new ProductPack(new Product(new Rectangular(3, 5, 7), 0.5), 4),
                    new Product(new Rectangular(2, 2, 2), 0.5),
                    new Product(new Rectangular(2, 3, 9), 0.5),
                ]
            ), true],
            [new Box(7, 8, 10), new ProductKit(
                [
                    new ProductPack(new Product(new Rectangular(3, 5, 7), 0.5), 4),
                    new ProductPack(new Product(new Rectangular(2, 2, 2), 0.5), 10),
                    new Product(new Rectangular(2, 3, 9), 0.5),
                ]
            ), true],
            [new Box(7, 8, 10), new ProductKit(
                [
                    new ProductPack(new Product(new Rectangular(3, 5, 7), 0.5), 4),
                    new ProductPack(new Product(new Rectangular(2, 2, 2), 0.5), 11),
                    new Product(new Rectangular(2, 3, 9), 0.5),
                ]
            ), false],
            [new Box(7, 8, 10), new ProductKit(
                [
                    new ProductPack(new Product(new Rectangular(3, 5, 7), 0.5), 6),
                    new Product(new Rectangular(2, 2, 2), 0.5),
                    new Product(new Rectangular(2, 3, 9), 0.5),
                ]
            ), false],
            [new Box(7, 8, 10), new ProductKit(
                [
                    new Product(new Rectangular(2, 2, 2), 0.5),
                    new Product(new Rectangular(2, 3, 11), 0.5),
                ]
            ), false],
        ];
    }

    /**
     * @param Box           $box
     * @param ItemInterface $item
     * @param bool          $expected
     *
     * @dataProvider getCanFitDataProvider
     */
    public function testCanFit(Box $box, ItemInterface $item, bool $expected)
    {
        $this->assertEquals($expected, $box->canFit($item));
    }
}
