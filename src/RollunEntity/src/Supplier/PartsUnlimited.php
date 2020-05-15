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

    /**
     * @inheritDoc
     */
    public function isInStock(string $rollunId): bool
    {
        $response = self::httpSend("api/datastore/PartsUnlimitedInventoryCacheDataStore?eq(rollun_id,$rollunId)&limit(20,0)");
        if (empty($response[0])) {
            return false;
        }

        $this->inventory = $response[0];

        return !empty($this->inventory['s_quantity']);
    }

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        /**
         * For all usps methods
         */
        $parts = explode('-Usps-', $shippingMethod);
        if (isset($parts[1])) {
            $uspsMethod = $parts[1];

            if ($item->getWeight() > 10) {
                return false;
            }

            if ((float)$item->getAttribute('your_dealer_price') > 100) {
                return false;
            }

            if (empty($item->getAttribute('nc_availability'))) {
                return false;
            }

            // @todo add air allowed

            if ($uspsMethod === 'FtCls-Package' && $item->getWeight() > 0.9) {
                return false;
            }

            if ($uspsMethod === 'PM-FR-Env' && $item->getWeight() > 5) {
                return false;
            }
        }

        return true;
    }
}
