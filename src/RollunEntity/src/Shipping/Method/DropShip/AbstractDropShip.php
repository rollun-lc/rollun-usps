<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip;

use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;

/**
 * Class AbstractDropShip
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class AbstractDropShip extends LevelBasedShippingMethod
{
    /**
     * AbstractDropShip constructor.
     *
     * @param string $shortName
     */
    public function __construct(string $shortName)
    {
        $this->shortName = $shortName;
    }
}
