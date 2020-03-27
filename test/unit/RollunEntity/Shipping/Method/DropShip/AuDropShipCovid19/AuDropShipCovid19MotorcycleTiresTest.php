<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\DropShip\AuDropShipCovid19;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19\AuDropShipCovid19MotorcycleTires as Shipping;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\CreateShippingRequestTrait;

/**
 * Class AuDropShipCovid19MotorcycleTiresTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AuDropShipCovid19MotorcycleTiresTest extends TestCase
{
    use CreateShippingRequestTrait;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(22, 12, 5, 0.2, '10002', '48204'), 8.25],
            [$this->createShippingRequest(20, 27, 5, 0.2, '10002', '48204'), 8.25],
            [$this->createShippingRequest(20, 27, 30, 0.2, '10002', '48204'), 8.25],
            [$this->createShippingRequest(20, 12, 5, 0.2, '10002', '48204', [Shipping::getKeyAttribute() => false]), null],
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
        $this->assertEquals($expected, (new Shipping('MOTORCYCLE-TIRES', [[999, 8.25]]))->getCost($shippingRequest));
    }
}
