<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class RmDropShip
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RmDropShip extends LevelBasedShippingMethod
{
    const MAX_WEIGHT = 70;

    /**
     * @var array
     */
    protected $levels
        = [
            // weight, price
            [9, 8.05], // If weight is 9 Lbs or less --> $8.05
        ];

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return $shippingRequest->item->getWeight() < self::MAX_WEIGHT;
    }

    /**
     * @return array
     */
    protected function getLevels(): array
    {
        $levels = parent::getLevels();

        $lastWeight = $levels[0][0];
        $lastCost = $levels[0][1];

        while ($lastWeight < self::MAX_WEIGHT) {
            $lastWeight++;
            $lastCost = $lastCost + 0.6; // +1 Lbs=+$0.6
            $levels[] = [$lastWeight, $lastCost];
        }

        return $levels;
    }

    /**
     * @inheritDoc
     */
    protected function isLevelValid(ShippingRequest $shippingRequest, array $level): bool
    {
        return $shippingRequest->item->getWeight() < $level[0];
    }

    /**
     * @inheritDoc
     */
    protected function getLevelCost(array $level): ?float
    {
        return round($level[1] * 1.06, 2); // 6% add for cost
    }
}
