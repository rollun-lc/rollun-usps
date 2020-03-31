<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19;

/**
 * Class AuDropShipCovid19MotorcycleTires
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AuDropShipCovid19MotorcycleTires extends AuDropShipCovid19AtvUtvTires
{
    /**
     * @var array
     */
    protected $levels
        = [
            // maxDiameter, price
            [999, 8.25]
        ];
}
