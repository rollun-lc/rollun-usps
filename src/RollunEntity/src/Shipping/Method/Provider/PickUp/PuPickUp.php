<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Provider\PickUp;

/**
 * Class PuPickUp
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class PuPickUp extends AbstractPickUpProvider
{
    /**
     * @inheritDoc
     */
    protected function getAddCost(): float
    {
        return 1.3;
    }

    /**
     * @inheritDoc
     */
    protected function getAllowedOriginationZips(): array
    {
        return ['28790'];
    }
}
