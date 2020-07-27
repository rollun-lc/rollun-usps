<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Shipping\ShippingRequest;

/**
 * Interface ShippingMethodInterface
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
interface ShippingMethodInterface
{
    /**
     * @param ShippingRequest $shippingRequest
     * @param bool            $shippingDataOnly
     *
     * @return float|null
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false);
}
