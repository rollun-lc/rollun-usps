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
    const ZIP_FROM = '04401';

    /**
     * @var array
     */
    protected $levels
        = [
            // weight, price
            [70, 9.5] // после 70 lbs - $9.5
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
        if ($this->isFirstClass($shippingRequest)) {
            // Если вес меньше 0.8 и вмещается в First-class - цена будет First-class
            $request = clone $shippingRequest;
            $request->addressOrigination = new Address(null, self::ZIP_FROM);
            $firstClass = new class ('FtCls-Package') extends Package {
                //https://trello.com/c/sx3qdqjY
                const USPS_BOXES = [['FtCls-Package', 'First-Class Package Service', 'FIRST CLASS COMMERCIAL', 'PACKAGE SERVICE', '', 22, 18, 15, 0.899]];
            };
            if ($firstClass->canBeShipped($request)) {
                $shippingCost = $firstClass->getCost($request, $shippingDataOnly);
            }
        }

        if (!isset($shippingCost)) {
            $shippingCost = $this->getStaticShippingCost($shippingRequest);
        }

        $largePackageSurcharge = $shippingRequest->getAttribute('largePackageSurcharge');

        if (!is_null($largePackageSurcharge)) {
            $shippingCost += $largePackageSurcharge;
        }

        return $shippingCost;
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

    private function getStaticShippingCost(ShippingRequest $shippingRequest): float
    {
        $flatRateGroup = $shippingRequest->getAttribute('flatRateGroup');

        switch ($flatRateGroup) {
            case 'ATV25':
                return 14.95;
            case 'ATV30':
                return 17.95;
            case 'ATV31':
                return 21.95;
            case 'LARGE':
                return 15;
            case 'MC1':
                return 9.95;
            case 'RIMS':
                return 10.5;
            case 'WES90':
            case 'WES91 ':
                return 29.95;
            default:
                return 9.5;
        }
    }

    private function isFirstClass(ShippingRequest $shippingRequest): bool
    {
        $firstClass = $shippingRequest->getAttribute('isFirstClass');

        if (is_null($firstClass)) {
            return $shippingRequest->item->getWeight() < 0.8;
        }

        return $firstClass;
    }
}
