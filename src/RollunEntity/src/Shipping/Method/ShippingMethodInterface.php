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

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return \DateTime|null
     */
    public function getTrackNumberDate(ShippingRequest $shippingRequest): ?\DateTime;

    /**
     * Date when package will send
     *
     * @param ShippingRequest $shippingRequest
     *
     * @return \DateTime|null
     */
    public function getShippingSendDate(ShippingRequest $shippingRequest): ?\DateTime;

    /**
     * Date when package will arrive
     *
     * @param ShippingRequest $shippingRequest
     *
     * @return \DateTime|null
     */
    public function getShippingArriveDate(ShippingRequest $shippingRequest): ?\DateTime;
}
