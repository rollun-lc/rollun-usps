<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Subject\Address;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\ShippingRequest;

class ShippingRequestTest extends TestCase
{

    public function testZip()
    {
        $addressOrigination = new Address('', '91601');
        $addressDestination = new Address('', '91730-1234');

        $rectangular = new Rectangular(10, 30, 5);
        $product = new Product($rectangular, 0.5);

        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination);

        $this->assertEquals(
                '91601', $shippingRequest->getOriginationZipCode()
        );
        $this->assertEquals(
                '91601', $shippingRequest->getOriginationZipCode(false)
        );
        $this->assertEquals(
                '91730-1234', $shippingRequest->getDestinationZipCode()
        );
        $this->assertEquals(
                '91730', $shippingRequest->getDestinationZipCode(false)
        );
    }
}
