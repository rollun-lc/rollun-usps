<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Subject\Address;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\Method\FixedPrice;
use rollun\Entity\Product\Container\Box;

class FixedPriceTest extends TestCase
{

    public function test_getShortName()
    {
        $box = new Box(35, 6, 11);
        $fixedPrice = new FixedPrice($box, 'Md1', 9.99, 20);
        $this->assertEquals(
                'Md1', $fixedPrice->getShortName()
        );
    }

    public function test_canBeShippedTrue()
    {
        $box = new Box(35, 6, 11);
        $fixedPrice = new FixedPrice($box, 'Md1', 9.99, 20);
        $this->assertEquals(
                'Md1', $fixedPrice->getShortName()
        );

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertTrue(
                $fixedPrice->canBeShipped($shippingRequest)
        );
    }

    public function test_canBeShippedFalse()
    {
        $box = new Box(35, 6, 11);
        $fixedPrice = new FixedPrice($box, 'Md1', 9.99, 20);
        $this->assertEquals(
                'Md1', $fixedPrice->getShortName()
        );

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(100, 300, 500);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertFalse(
                $fixedPrice->canBeShipped($shippingRequest)
        );
    }

    public function test_getShippingMetods_Null()
    {
        $box = new Box(35, 6, 11);
        $fixedPrice = new FixedPrice($box, 'Md1', 9.99, 20);
        $this->assertEquals(
                'Md1', $fixedPrice->getShortName()
        );

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(100, 300, 500);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(
                ['id' => 'Md1', 'cost' => null, 'Error' => null], $fixedPrice->getShippingMetods($shippingRequest)->getArrayCopy()[0]
        );
    }

    public function test_getShippingMetods_99()
    {
        $box = new Box(35, 6, 11);
        $fixedPrice = new FixedPrice($box, 'Md1', 99, 20);
        $this->assertEquals(
                'Md1', $fixedPrice->getShortName()
        );

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(
                ['id' => 'Md1', 'cost' => 20, 'Error' => null], $fixedPrice->getShippingMetods($shippingRequest)->getArrayCopy()[0]
        );
    }
}
