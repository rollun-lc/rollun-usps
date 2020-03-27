<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19;

use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class AbstractAuDropShipCovid19
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class AbstractAuDropShipCovid19 extends LevelBasedShippingMethod
{
    /**
     * @var array
     */
    protected $levels = [];

    /**
     * Attribute for enable/disable of shipping method
     *
     * @return string
     */
    abstract public static function getKeyAttribute(): string;

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return !(isset($shippingRequest->getAttributes()[$this->getKeyAttribute()]) && empty($shippingRequest->getAttribute($this->getKeyAttribute())));
    }

    /**
     * @inheritDoc
     */
    protected function isLevelValid(ShippingRequest $shippingRequest, array $level): bool
    {
        return $level[0] >= $this->getDiameter($shippingRequest);
    }

    /**
     * @inheritDoc
     */
    protected function getLevelCost(array $level): ?float
    {
        return $level[1];
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return float
     */
    protected function getDiameter(ShippingRequest $shippingRequest): float
    {
        return (float)$shippingRequest->item->getDimensionsList()[0]['dimensions']->max;
    }
}
