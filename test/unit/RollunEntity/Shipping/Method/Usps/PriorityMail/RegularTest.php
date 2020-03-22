<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Shipping\Method\Usps\PriorityMail\Regular;
use rollun\test\unit\Entity\Shipping\Method\Usps\UspsShippingAbstract;

/**
 * Class RegularTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RegularTest extends UspsShippingAbstract
{
    /**
     * @var string
     */
    protected $class = Regular::class;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(11, 11, 11, 0.2, '10002', '48204')],
            [$this->createShippingRequest(12, 12, 12, 65, '90001', '90211')],
            [$this->createShippingRequest(1, 1, 1, 30, '10002', '48204')],
            [$this->createShippingRequest(17, 16, 12, 2, '90001', '90211')],
            [$this->createShippingRequest(1, 104, 1, 0.2, '90001', '90211')],
            [$this->createShippingRequest(2, 2, 2, 65, '90001', '90211')],
            // canBeShipped should return false
            [$this->createShippingRequest(1, 105, 1, 0.2, '90001', '90211')],
            [$this->createShippingRequest(2, 2, 2, 71, '90001', '90211')],
        ];
    }
}
