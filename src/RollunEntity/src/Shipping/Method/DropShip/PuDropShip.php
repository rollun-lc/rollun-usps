<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\ShippingMethodAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class PuDropShip
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class PuDropShip extends ShippingMethodAbstract
{
    const BASE_COST = 8.5;

    /**
     * 1) For UPS length plus girth [(2 x width) + (2 x height)] combined exceeds 118 inches, but does not exceed the maximum size of 157 inches
     * 2) For FedEx exceeds 96 inches in length or 130 inches in length and girth
     * 3) >70 lbs
     */
    const OVERSIZE_COST = 20;

    /**
     * RmDropShip constructor.
     */
    public function __construct()
    {
        $this->shortName = 'PU-DS';
    }

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        /** @var array $dimensions */
        $dimensions = $shippingRequest->item->getDimensionsList()[0]['dimensions']->getDimensionsRecord();

        // For UPS length plus girth [(2 x width) + (2 x height)] combined exceeds 118 inches, but does not exceed the maximum size of 157 inches
        if (($dimensions['Girth'] + $dimensions['Length']) > 118) {
            return self::OVERSIZE_COST;
        }

        // For FedEx exceeds 96 inches in length or 130 inches in length and girth
        if ($dimensions['Length'] > 96 || ($dimensions['Girth'] + $dimensions['Length']) > 130) {
            return self::OVERSIZE_COST;
        }

        // >70 lbs
        if ($this->getLbs($shippingRequest) > 70) {
            return self::OVERSIZE_COST;
        }

        // @todo if the tire (commodity_code from 0301 to 0329), than $14

        return self::BASE_COST;
    }
}
