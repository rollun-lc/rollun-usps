<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Subject\Address;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\Method\Usps\FirstClass\Package;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Shipping\Method\ShippingMethodProvider;
use rollun\Entity\Shipping\Method\Usps\UspsProvider;

class PackageTest extends TestCase
{

    public function test_getShortName()
    {
        $firstClassPackage = new Package('FtCls-Package');
        $this->assertEquals(
            'FtCls-Package', $firstClassPackage->getShortName()
        );
    }

    public function test_getShippingMetods()
    {
        $firstClassPackage = new Package('FtCls-Package');

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(12, 10, 5);
        $product = new Product($rectangular, 0.5);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(
            12, $firstClassPackage->getShippingData($shippingRequest)['Length']
        );
        $this->assertTrue(
            5 > $firstClassPackage->getCost($shippingRequest)
        );
    }


    public function canBeShippedTrueDataProvider(): array
    {
        return [
            [0.5],
            [0.25],
            [0.2],
            [0.8],
            [0.79],
            [0.79],
        ];
    }

    /**
     * @param $weight
     * @dataProvider canBeShippedTrueDataProvider
     */
    public function testCanBeShippedTrue($weight): void
    {
        $firstClassPackage = new Package('FtCls-Package');
        $addressOrigination = new Address('', '84655');
        $addressDestination = new Address('', '91430');

        $rectangular = new Rectangular(3, 3, 2.75);
        $product = new Product($rectangular, $weight);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);
        $this->assertEquals(true, $firstClassPackage->canBeShipped($shippingRequest));
    }


    public function canBeShippedFalseDataProvider(): array
    {
        return [
            [0.81],
            [1],
            [0.9],
        ];
    }


    /**
     * @param $weight
     * @dataProvider canBeShippedFalseDataProvider
     */
    public function testCanBeShippedFalse($weight): void
    {
        $firstClassPackage = new Package('FtCls-Package');

        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(12, 10, 5);
        $product = new Product($rectangular, $weight);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(false, $firstClassPackage->canBeShipped($shippingRequest));
    }
}
