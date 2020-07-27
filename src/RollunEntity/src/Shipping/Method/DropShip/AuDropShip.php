<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;
use rollun\Entity\Shipping\Method\Usps\FirstClass\Package;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Subject\Address;

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
    const ZIP_FROM = '46268';

    /**
     * @var array
     */
    protected $levels
        = [
            // weight, price
            [7, 8.5], // до 7 lbs - $8.50
            [70, 10] // после 7 lbs - $10
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
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        // Если вмещается в First-class- цена будет First-class
        $request = clone $shippingRequest;
        $request->addressOrigination = new Address(null, self::ZIP_FROM);
        $firstClass = new Package('FtCls-Package');
        if ($firstClass->canBeShipped($request)) {
            return $firstClass->getCost($request, $shippingDataOnly);
        }

        return parent::getCost($shippingRequest, $shippingDataOnly);
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
