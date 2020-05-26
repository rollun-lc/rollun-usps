<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class AuDropShip
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AuDropShip extends LevelBasedShippingMethod
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
            // weight, price
            [12 / 16, 5], // 1-12 oz $5
            [15.99 / 16, 6], // 12.01 – 15.99 oz $6
            [70, 10.50] // 1 – 69 lbs $10.50
        ];

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return $shippingRequest->item->getWeight() < self::MAX_WEIGHT;
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
        return $level[1];
    }
}
