<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Subject\Address;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\Method\Usps\FirstClass\Package;
use rollun\Entity\Shipping\Method\Usps\UspsProvider;

/**
 * Class PackageTest
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class PackageTest extends TestCase
{
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

    /**
     * Is locally calculated cost is the same as api cost
     *
     * @param ShippingRequest $shippingRequest
     *
     * @dataProvider shippingRequestsDataProvider
     */
    public function testIsCostMatch(ShippingRequest $shippingRequest)
    {
        $this->assertEquals($this->getCost($shippingRequest), $this->getCost($shippingRequest, false));
    }

    /**
     * @param float  $width
     * @param float  $length
     * @param float  $height
     * @param float  $weight
     * @param string $zipFrom
     * @param string $zipTo
     *
     * @return ShippingRequest
     */
    protected function createShippingRequest(float $width, float $length, float $height, float $weight, string $zipFrom, string $zipTo): ShippingRequest
    {
        $addressOrigination = new Address('', $zipFrom);
        $addressDestination = new Address('', $zipTo);

        $rectangular = new Rectangular($length, $width, $height);
        $product = new Product($rectangular, $weight);

        return new ShippingRequest($product, $addressOrigination, $addressDestination);
    }

    /**
     * @param ShippingRequest $shippingRequest
     * @param bool            $definedCost
     *
     * @return float|null
     */
    protected function getCost(ShippingRequest $shippingRequest, bool $definedCost = true): ?float
    {
        $shippingMethods = [];
        foreach (Package::getAllShortNames() as $shortName) {
            $shippingMethods[] = (new Package($shortName))->setDefinedCost($definedCost);
        }

        $data = (new UspsProvider($shippingMethods))->getShippingMetods($shippingRequest);
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        return (isset($data[0]['cost'])) ? (float)$data[0]['cost'] : null;
    }
}
