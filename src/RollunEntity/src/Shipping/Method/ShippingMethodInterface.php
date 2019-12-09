<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\ShippingResponseSet;

interface ShippingMethodInterface
{

    /**
     *
     * @param ShippingRequest $shippingRequest
     * @return ShippingResponseSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89],[['id'  =>...]]
     */
    public function getShippingMetods(ShippingRequest $shippingRequest): ShippingResponseSet;

    /**
     *
     * @return string 'USPS_FR_Md1' for USPS FlatRate Middle Box 1
     */
    public function getShortName(): string;
}
