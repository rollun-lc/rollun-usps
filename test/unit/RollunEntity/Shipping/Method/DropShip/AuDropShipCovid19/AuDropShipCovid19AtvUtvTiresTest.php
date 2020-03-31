<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\DropShip\AuDropShipCovid19;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19\AuDropShipCovid19AtvUtvTires as Shipping;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\CreateShippingRequestTrait;

/**
 * Class AuDropShipCovid19AtvUtvTiresTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AuDropShipCovid19AtvUtvTiresTest extends TestCase
{
    use CreateShippingRequestTrait;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(22, 12, 5, 0.2, '10002', '48204'), 12.95],
            [$this->createShippingRequest(22, 27, 5, 0.2, '10002', '48204'), 15.95],
            [$this->createShippingRequest(30, 27, 5, 0.2, '10002', '48204'), 17.95],
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
        $this->assertEquals($expected, (new Shipping('ATV/UTV-TIRES', [[25, 12.95], [27, 15.95], [999, 17.95]]))->getCost($shippingRequest));
    }
}
