<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\DropShip;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Shipping\Method\DropShip\AuDropShip;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\CreateShippingRequestTrait;

/**
 * Class AuDropShipTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AuDropShipTest extends TestCase
{
    use CreateShippingRequestTrait;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(20, 12, 5, 0.2, '10002', '48204'), 2.76],
            [$this->createShippingRequest(20, 12, 5, 0.8, '10002', '48204'), 5.08],
            [$this->createShippingRequest(20, 12, 5, 22, '10002', '48204'), 10.5],
            [$this->createShippingRequest(20, 12, 5, 75, '10002', '48204'), null],
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
        $this->assertEquals($expected, (new AuDropShip('AU-DS', [[12 / 16, 5], [15.99 / 16, 6], [70, 10.50]]))->getCost($shippingRequest));
    }
}
