<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps\FirstClass;

use rollun\Entity\Shipping\Method\Usps\FirstClass\Package;
use rollun\test\unit\Entity\Shipping\Method\Usps\UspsShippingAbstract;

/**
 * Class PackageTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class PackageTest extends UspsShippingAbstract
{
    /**
     * @var string
     */
    protected $class = Package::class;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(10, 12, 5, 0.5, '10002', '48204')],
            [$this->createShippingRequest(21, 17, 2, 0.2, '10002', '48204')],
            [$this->createShippingRequest(23, 17, 2, 0.2, '10002', '48204')],
            [$this->createShippingRequest(2, 2, 2, 1.1, '90001', '90211')],
            [$this->createShippingRequest(10, 12, 5, 0.5, '90001', '90211')],
        ];
    }
}
