<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class WpsDropShip
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class WpsDropShip extends LevelBasedShippingMethod
{
    /**
     * @var array
     */
    protected $levels
        = [
            // weight, price
            [40, 16], // $16 DS - до 40 lbs
            [999, 25] // $25 DS больше 40 lbs
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
