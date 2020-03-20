<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\DropShip;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Shipping\Method\DropShip\RmDropShip;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\CreateShippingRequestTrait;

/**
 * Class RmDropShipTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RmDropShipTest extends TestCase
{
    use CreateShippingRequestTrait;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(2, 2, 5, 2, '10002', '48204'), 8.53],
            [$this->createShippingRequest(2, 2, 5, 7, '10002', '48204'), 8.53],
            [$this->createShippingRequest(2, 2, 5, 12, '10002', '48204'), 11.08],
            [$this->createShippingRequest(2, 2, 5, 55, '10002', '48204'), 38.43],
            [$this->createShippingRequest(2, 2, 5, 75, '10002', '48204'), null],
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
        $this->assertEquals($expected, (new RmDropShip('RM-DS', [[9, 8.05]]))->getCost($shippingRequest));
    }
}
