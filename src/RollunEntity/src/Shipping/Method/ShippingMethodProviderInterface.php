<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\ShippingResponseSet;

/**
 * Class ShippingMethodProviderInterface
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
interface ShippingMethodProviderInterface
{
    /**
     *
     * @param ShippingRequest $shippingRequest
     *
     * @return ShippingResponseSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89],[['id'  =>...]]
     */
    public function getShippingMethods(ShippingRequest $shippingRequest): ShippingResponseSet;

    /**
     *
     * @return string 'USPS_FR_Md1' for USPS FlatRate Middle Box 1
     */
    public function getShortName(): string;
}
