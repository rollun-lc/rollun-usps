<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Product\Item;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ProductKit;

class ProductKitTest extends TestCase
{

    public function testProductKitWeight()
    {
        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $productPack = new ProductPack($product, 4);
        $productKit = new ProductKit([$productPack, $product]);

        $this->assertEquals(
                0.5 + 0.5 * 4, $productKit->getWeight()
        );
    }

    public function testProductKitVolume()
    {
        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $productPack = new ProductPack($product, 4);
        $productKit = new ProductKit([$productPack, $product]);

        $this->assertEquals(
                10 * 30 * 5 + 10 * 30 * 5 * 4, $productKit->getVolume()
        );
    }
}
