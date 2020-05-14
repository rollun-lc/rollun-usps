<?php
declare(strict_types=1);

namespace rollun\Entity\Supplier;

use rollun\Entity\Product\Item\ItemInterface;

/**
 * Class PartsUnlimited
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class PartsUnlimited extends AbstractSupplier
{
    /**
     * @var string
     */
    protected $zipOriginal = '28790';

    /**
     * @var array
     */
    protected $shippingMethods
        = [
            [
                'name'     => 'Root-PU-PickUp-Usps-FtCls-Package',
                'priority' => 1
            ],
            [
                'name'     => 'Root-PU-PickUp-Usps-PM-FR-Env',
                'priority' => 3
            ],
            [
                'name'     => 'Root-PU-DS',
                'priority' => 4
            ],
        ];
}
