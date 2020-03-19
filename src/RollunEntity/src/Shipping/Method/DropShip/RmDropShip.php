<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\ShippingMethodAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class RmDropShip
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RmDropShip extends ShippingMethodAbstract
{
    const BASE_COST = 8.05;
    const MAX_WEIGHT_FOR_BASE_COST = 9;
    const MARGIN_OVERSIZE = 0.6;
    const MARGIN_PERCENT = 6;

    /**
     * RmDropShip constructor.
     */
    public function __construct()
    {
        $this->shortName = 'RM-DS';
    }

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return true;
    }

    /**
     * If weight is 9 Lbs or less --> $8.05 then +1 Lbs=+$0.6
     * 6% add for cost
     *
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        $cost = self::BASE_COST;

        $lbs = $this->getLbs($shippingRequest);

        $i = self::MAX_WEIGHT_FOR_BASE_COST;
        while ($i < $lbs) {
            // add oversize margin
            $cost = $cost + self::MARGIN_OVERSIZE;

            $i++;
        }

        // add percent margin
        $cost = ($cost * self::MARGIN_PERCENT / 100) + $cost;

        return round($cost, 2);
    }
}
