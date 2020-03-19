<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Provider\PickUp;

/**
 * Class RmPickUp
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RmPickUp extends AbstractPickUpProvider
{
    /**
     * @inheritDoc
     */
    protected function getAddCost(): float
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    protected function getAllowedOriginationZips(): array
    {
        return ['84663'];
    }
}
