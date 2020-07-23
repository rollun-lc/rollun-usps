<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Factory;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Subject\Address;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\ShippingRequest;

class FixedPriceAbstractFactoryTest extends TestCase
{

    public function test_getShortName()
    {
        global $container;
        $fixedPrice = $container->get('Priority Mail Medium Flat Rate Box 2');
        $this->assertEquals(
                'FrMb2', $fixedPrice->getShortName()
        );
    }

    public function test_getShippingMetodsProviderInProvider()
    {
        global $container;
        $providerUsps = $container->get('UspsTest');

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(
                ['id' => 'UspsTest-Md1', 'cost' => 10, 'Error' => null], $providerUsps->getShippingMethods($shippingRequest)->getArrayCopy()[0]
        );

        $this->assertEquals(
                ['id' => 'UspsTest-Md2', 'cost' => null, 'Error' => null], $providerUsps->getShippingMethods($shippingRequest)->getArrayCopy()[1]
        );
    }
}
