<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Shipping\Method\Usps\PriorityMail\RegionalRate;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\Usps\UspsShippingAbstract;

/**
 * Class RegionalRateTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RegionalRateTest extends UspsShippingAbstract
{
    /**
     * @var string
     */
    protected $class = RegionalRate::class;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(1, 1, 1, 0.5, '10002', '48204')],
            [$this->createShippingRequest(10, 12, 1, 5, '90001', '90211')],
            [$this->createShippingRequest(12, 10, 4, 14, '10002', '48204')],
            [$this->createShippingRequest(14, 14, 1, 20, '90001', '90211')],
            // canBeShipped should return false
            [$this->createShippingRequest(2, 2, 2, 61, '90001', '90211')],
            [$this->createShippingRequest(16, 1, 1, 10, '90001', '90211')],
        ];
    }

    /**
     * Is locally calculated cost is the same as api cost
     *
     * @param ShippingRequest $shippingRequest
     *
     * @dataProvider shippingRequestsDataProvider
     */
    public function testIsCostMatch(ShippingRequest $shippingRequest)
    {
        $this->assertEquals($this->getCost($shippingRequest, false), $this->getCost($shippingRequest));
    }
}
