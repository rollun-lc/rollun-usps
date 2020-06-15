<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Shipping\Method\Usps\PriorityMail\Cubic;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\test\unit\Entity\Shipping\Method\Usps\UspsShippingAbstract;

/**
 * Class RegularTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class CubicTest extends UspsShippingAbstract
{
    /**
     * @var string
     */
    protected $class = Cubic::class;

    /**
     * @return array
     */
    public function shippingRequestsDataProvider(): array
    {
        return [
            [$this->createShippingRequest(5, 1, 5, 2.39, '24551', '84663', ['expected' => 8.15])],
            [$this->createShippingRequest(5, 11.25, 4, 2.47, '26187', '84663', ['expected' => 9.22])],
            [$this->createShippingRequest(13, 3, 12, 5.65, '87507', '84663', ['expected' => 8.55])],
            [$this->createShippingRequest(16.5, 1.25, 14, 3.8, '44070', '84663', ['expected' => 9.22])],
            [$this->createShippingRequest(15, 0.5, 12.25, 1.031, '14445', '84663', ['expected' => 8.15])],
            [$this->createShippingRequest(14.75, 3.25, 13, 4.7, '95310', '84663', ['expected' => 8.93])],
            [$this->createShippingRequest(5, 1, 5, 2.39, '15085', '84663', ['expected' => 8.15])],
            [$this->createShippingRequest(14, 1, 11, 2.12, '65050', '84663', ['expected' => 7.8])],
            [$this->createShippingRequest(15, 1, 5, 1.526, '85256', '84663', ['expected' => 7.56])],
            [$this->createShippingRequest(15, 18.1, 5, 1.526, '85256', '84663', ['expected' => 0])],
            [$this->createShippingRequest(5, 1, 5, 70.2, '24551', '84663', ['expected' => 0])],
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
        $this->assertEquals(['Usps-PM-Cubic' => $shippingRequest->getAttribute('expected')], $this->getCost($shippingRequest));
    }
}
