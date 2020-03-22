<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class PuDropShip
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class PuDropShip extends LevelBasedShippingMethod
{
    const MAX_WEIGHT = 70;

    /**
     * @var array
     */
    protected $levels
        = [
            // isOversize, price
            [false, 8.5],
            [true, 20],
        ];

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
    protected function isLevelValid(ShippingRequest $shippingRequest, array $level): bool
    {
        return $this->isOversize($shippingRequest) === $level[0];
    }

    /**
     * @inheritDoc
     */
    protected function getLevelCost(array $level): ?float
    {
        return $level[1];
    }

    /**
     * Length plus girth [(2 x width) + (2 x height)] combined exceeds 118 inches, but does not exceed the maximum size of 157 inches
     * OR exceeds 96 inches in length or 130 inches in length and girth
     * OR >70 lbs
     *
     * @param ShippingRequest $shippingRequest
     *
     * @return bool
     */
    protected function isOversize(ShippingRequest $shippingRequest): bool
    {
        if ($shippingRequest->item->getWeight() > self::MAX_WEIGHT) {
            return true;
        }

        /** @var array $dimensions */
        $dimensions = $shippingRequest->item->getDimensionsList()[0]['dimensions']->getDimensionsRecord();

        // For UPS length plus girth [(2 x width) + (2 x height)] combined exceeds 118 inches, but does not exceed the maximum size of 157 inches
        if (($dimensions['Girth'] + $dimensions['Length']) > 118) {
            return true;
        }

        // For FedEx exceeds 96 inches in length or 130 inches in length and girth
        if ($dimensions['Length'] > 96) {
            return true;
        }

        // @todo if the tire (commodity_code from 0301 to 0329), than $14

        return false;
    }
}
