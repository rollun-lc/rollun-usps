<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19;

/**
 * Class AuDropShipCovid19AtvUtvTires
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AuDropShipCovid19AtvUtvTires extends AbstractAuDropShipCovid19
{
    /**
     * @var array
     */
    protected $levels
        = [
            // maxDiameter, price
            [25, 12.95],
            [27, 15.95],
            [999, 17.95]
        ];

    /**
     * @inheritDoc
     */
    public static function getKeyAttribute(): string
    {
        return 'isTire';
    }
}
