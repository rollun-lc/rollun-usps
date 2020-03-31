<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19;

/**
 * Class AuDropShipCovid19AtvUtvWheels
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AuDropShipCovid19AtvUtvWheels extends AbstractAuDropShipCovid19
{
    /**
     * @var array
     */
    protected $levels
        = [
            // maxDiameter, price
            [999, 12.95]
        ];

    /**
     * @inheritDoc
     */
    public static function getKeyAttribute(): string
    {
        return 'isWheel';
    }
}
