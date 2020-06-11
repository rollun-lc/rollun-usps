<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method\Usps;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\Method\Usps\UspsProvider;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Subject\Address;
use rollun\test\unit\Entity\Shipping\Method\CreateShippingRequestTrait;

/**
 * Class UspsShippingAbstract
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class UspsShippingAbstract extends TestCase
{
    use CreateShippingRequestTrait;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param ShippingRequest $shippingRequest
     * @param bool            $definedCost
     *
     * @return array
     */
    protected function getCost(ShippingRequest $shippingRequest, bool $definedCost = true): array
    {
        $class = $this->class;

        $shippingMethods = [];
        foreach ($class::getAllShortNames() as $shortName) {
            $shippingMethods[] = (new $class($shortName))->setDefinedCost($definedCost);
        }

        $data = (new UspsProvider($shippingMethods))->getShippingMetods($shippingRequest)->toArray();

        $result = array_column($data, 'cost', 'id');
        ksort($result);

        foreach ($result as $k => $v) {
            $result[$k] = (float)$v;
        }

        return $result;
    }
}
