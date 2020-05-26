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
use rollun\Entity\Shipping\Method\ShippingMethodProvider;

class ShippingMethodProviderTest extends TestCase
{

    public function test_getShortName()
    {
        $box1 = new Box(35, 6, 11);
        $fixedPrice1 = new FixedPrice($box1, 'Md1', 10, 20);
        $box2 = new Box(10, 10, 10);
        $fixedPrice2 = new FixedPrice($box2, 'Md2', 20, 20);

        $provider = new ShippingMethodProvider('Fr', [$fixedPrice1, $fixedPrice2]);
        $this->assertEquals(
                'Fr', $provider->getShortName()
        );
    }

    public function test_getShippingMetods()
    {
        $box1 = new Box(35, 6, 11);
        $fixedPrice1 = new FixedPrice($box1, 'Md1', 10, 20);
        $box2 = new Box(10, 10, 10);
        $fixedPrice2 = new FixedPrice($box2, 'Md2', 20, 20);

        $provider = new ShippingMethodProvider('Fr', [$fixedPrice1, $fixedPrice2]);

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(
                ['id' => 'Fr-Md1', 'cost' => 20, 'Error' => null, 'name' => ''], $provider->getShippingMetods($shippingRequest)->getArrayCopy()[0]
        );

        $this->assertEquals(
                ['id' => 'Fr-Md2', 'cost' => null, 'Error' => null, 'name' => ''], $provider->getShippingMetods($shippingRequest)->getArrayCopy()[1]
        );
    }

    public function test_getShippingMetodsProviderInProvider()
    {
        $box1 = new Box(35, 6, 11);
        $fixedPrice1 = new FixedPrice($box1, 'Md1', 10, 20);
        $box2 = new Box(10, 10, 10);
        $fixedPrice2 = new FixedPrice($box2, 'Md2', 20, 20);

        $providerFr = new ShippingMethodProvider('Fr', [$fixedPrice1, $fixedPrice2]);

        $providerUsps = new ShippingMethodProvider('Usps', [$providerFr]);

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(
                ['id' => 'Usps-Fr-Md1', 'cost' => 20, 'Error' => null, 'name' => ''], $providerUsps->getShippingMetods($shippingRequest)->getArrayCopy()[0]
        );

        $this->assertEquals(
                ['id' => 'Usps-Fr-Md2', 'cost' => null, 'Error' => null, 'name' => ''], $providerUsps->getShippingMetods($shippingRequest)->getArrayCopy()[1]
        );
    }
}
