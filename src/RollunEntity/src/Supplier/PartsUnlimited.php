<?php
declare(strict_types=1);

namespace rollun\Entity\Supplier;

use rollun\Entity\Product\Item\ItemInterface;

/**
 * Class PartsUnlimited
 *
 * @author r.ratsun <r.ratsun.rollun@gmail.com>
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
                'name'     => 'PU-PickUp-Usps-FtCls-Package',
                'priority' => 1
            ],
            [
                'name'     => 'PU-PickUp-Usps-PM-FR-Env',
                'priority' => 3
            ],
            [
                'name'     => 'PU-DS',
                'priority' => 4
            ],
        ];

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        /**
         * For all usps methods
         */
        if ($shippingMethod !== 'PU-DS') {
            if ($item->getWeight() >= 10) {
                return false;
            }

            if ((float)$item->getAttribute('price') > 100) {
                return false;
            }
            if (empty($item->getAttribute('airAllowed'))) {
                return false;
            }
        }

        if ($shippingMethod === 'PU-PickUp-Usps-FtCls-Package' && $item->getWeight() > 0.9) {
            return false;
        }

        if ($shippingMethod === 'PU-PickUp-Usps-PM-FR-Env' && $item->getWeight() > 5) {
            return false;
        }

        return true;
    }
}
