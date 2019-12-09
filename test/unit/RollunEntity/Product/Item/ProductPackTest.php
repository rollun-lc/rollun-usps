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

class ProductPackTest extends TestCase
{

    public function testProductPack()
    {
        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $productPack = new ProductPack($product, 4);

        $this->assertEquals(
                'Rectangular', $productPack->getDimensionsList()[0]['dimensions']->getDimensionsRecord()['Type']
        );
        $this->assertEquals(
                4, $productPack->getDimensionsList()[0]['quantity']
        );
        $this->assertEquals(
                0.5 * 4, $productPack->getWeight()
        );
    }
}
