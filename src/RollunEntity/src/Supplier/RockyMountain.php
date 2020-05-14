<?php
declare(strict_types=1);

namespace rollun\Entity\Supplier;

use rollun\Entity\Product\Item\ItemInterface;

/**
 * Class RockyMountain
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RockyMountain extends AbstractSupplier
{
    /**
     * @var string
     */
    protected $zipOriginal = '84663';

    /**
     * @var array
     */
    protected $shippingMethods
        = [
            [
                'name'     => 'Root-RM-PickUp-Usps-FtCls-Package',
                'priority' => 1
            ],
            [
                'name'     => 'Root-RM-DS-Ontrack',
                'priority' => 2
            ],
            [
                'name'     => 'Root-RM-PickUp-Usps-PM-FR-Env',
                'priority' => 3
            ],
            [
                'name'     => 'Root-RM-DS',
                'priority' => 7
            ],
        ];

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        if ($shippingMethod === 'RM-DS-Ontrack' && empty($item->quantity)) {
            return false;
        }

        return parent::isValid($item, $zipDestination, $shippingMethod);
    }
}
