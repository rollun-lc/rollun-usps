<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Shipping\Method\Usps\PriorityMail\FlatRate;
use rollun\test\unit\Entity\Shipping\Method\Usps\UspsShippingAbstract;

/**
 * Class RegionalRateTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class FlatRateTest extends UspsShippingAbstract
{
    /**
     * @var string
     */
    protected $class = FlatRate::class;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(1, 1, 1, 20, '10002', '48204')],
            [$this->createShippingRequest(10, 12, 1, 5, '90001', '90211')],
            [$this->createShippingRequest(20, 10, 2, 70, '10002', '48204')],
            // canBeShipped should return false
            [$this->createShippingRequest(25, 2, 2, 2, '90001', '90211')],
            [$this->createShippingRequest(1, 1, 1, 71, '90001', '90211')],
        ];
    }
}
