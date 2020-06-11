<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps\ParcelSelect;

use rollun\Entity\Shipping\Method\Usps\ParcelSelect\Ground;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\Usps\UspsShippingAbstract;

/**
 * Class GroundTest
 *
 * @author r.ratsun <r.ratsun.rollun@gmail.com>
 */
class GroundTest extends UspsShippingAbstract
{
    /**
     * @var string
     */
    protected $class = Ground::class;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(2, 2, 1, 5, '10002', '48204'), 9.29],
            [$this->createShippingRequest(25, 20, 20, 5, '10002', '48204'), 51.02],
            [$this->createShippingRequest(25, 22, 22, 5, '10002', '48204'), 118.75],
            [$this->createShippingRequest(15, 10, 8, 62, '10002', '98204'), 122.53],
            [$this->createShippingRequest(2, 1, 1, 1, '10002', '98204'), 8.12],
            [$this->createShippingRequest(32, 25, 25, 11, '10002', '98204'), 0],
        ];
    }

    /**
     * Is locally calculated cost is the same as api cost
     *
     * @param ShippingRequest $shippingRequest
     * @param float           $expected
     *
     * @dataProvider shippingRequestsDataProvider
     */
    public function testIsCostMatch(ShippingRequest $shippingRequest, float $expected)
    {
        $this->assertEquals(['Usps-PS-Ground' => $expected], $this->getCost($shippingRequest));
    }
}
