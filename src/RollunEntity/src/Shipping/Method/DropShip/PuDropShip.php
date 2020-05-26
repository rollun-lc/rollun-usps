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
     * @var string
     */
    protected $name = 'Drop Shipping';

    /**
     * @var array
     */
    protected $levels
        = [
            // isOversize, commodity_code_range, price
            [null, [301, 329], 14],
            [false, null, 8.5],
            [true, null, 20],
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
        // if the tire (commodity_code from 0301 to 0329), than $14
        if ($level[1] !== null && is_array($level[1])) {
            foreach (['CommodityCode', 'commodity_code'] as $attr) {
                if (!empty($value = $shippingRequest->getAttribute($attr))) {
                    $value = (int)$value;
                    if ($value >= $level[1][0] && $value <= $level[1][1]) {
                        return true;
                    }
                }
            }
        }

        return $this->isOversize($shippingRequest) === $level[0];
    }

    /**
     * @inheritDoc
     */
    protected function getLevelCost(array $level): ?float
    {
        return $level[2];
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

        return false;
    }
}
