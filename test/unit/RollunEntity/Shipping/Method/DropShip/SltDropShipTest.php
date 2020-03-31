<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\DropShip;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Shipping\Method\DropShip\SltDropShip;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\CreateShippingRequestTrait;

/**
 * Class SltDropShipTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class SltDropShipTest extends TestCase
{
    use CreateShippingRequestTrait;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(2, 2, 5, 2, '10002', '48204'), 10],
            [$this->createShippingRequest(50, 40, 5, 3, '10002', '48204'), 10],
            [$this->createShippingRequest(100, 10, 2, 3, '10002', '48204'), 10],
        ];
    }

    /**
     * @param ShippingRequest $shippingRequest
     * @param float|null      $expected
     *
     * @dataProvider shippingRequestsDataProvider
     */
    public function testGetCost(ShippingRequest $shippingRequest, $expected)
    {
        $this->assertEquals($expected, (new SltDropShip('SLT-DS', [[10]]))->getCost($shippingRequest));
    }
}
