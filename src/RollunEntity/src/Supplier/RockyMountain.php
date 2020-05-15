<?php
declare(strict_types=1);

namespace rollun\Entity\Supplier;

use rollun\Entity\Product\Item\ItemInterface;
use service\Entity\Api\DataStore\Shipping\BestShipping;

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
                'name'     => 'Root-RM-PickUp-Usps-PM-FR-LegalEnv',
                'priority' => 5
            ],
            [
                'name'     => 'Root-RM-PickUp-Usps-PM-FR-Pad-Env',
                'priority' => 6
            ],
            [
                'name'     => 'Root-RM-DS',
                'priority' => 7
            ],
        ];

    /**
     * @inheritDoc
     */
    public function isInStock(string $rollunId): bool
    {
        $response = BestShipping::httpSend("api/datastore/RockyMountainInventoryCacheDataStore?eq(rollun_id,$rollunId)&limit(20,0)");
        if (empty($response[0])) {
            return false;
        }

        $this->inventory = $response[0];

        return !empty($this->inventory['qty_ut']) || !empty($this->inventory['qty_ky']);
    }

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        if ($shippingMethod === 'Root-RM-DS-Ontrack' && empty($item->getAttribute('qty_ut'))) {
            return false;
        }

        /**
         * For all usps methods
         */
        $parts = explode('-Usps-', $shippingMethod);
        if (isset($parts[1])) {
            if (empty($item->getAttribute('isAirAllowed'))) {
                return false;
            }

            if ($item->getWeight() > 20) {
                return false;
            }

            if ((float)$item->getAttribute('rmatv_price') > 100) {
                return false;
            }

            if (empty($item->getAttribute('qty_ut'))) {
                return false;
            }

            if ($parts[1] === 'FtCls-Package' && $item->getWeight() > 0.9) {
                return false;
            }

            // get item dimensions
            $dimensions = $item->getDimensionsList()[0]['dimensions'];

            $weight = $item->getWeight();
            $lbs = $item->getVolume() / 166;
            if ($lbs > $item->getWeight()) {
                $weight = $lbs;
            }

            if ($parts[1] === 'PM-FR-Env') {
                if ($dimensions->max <= 0) {
                    return false;
                }

                if ($weight > 5) {
                    return false;
                }
            }

            if ($parts[1] === 'PM-FR-LegalEnv' || $parts[1] === 'PM-FR-Pad-Env') {
                if ($dimensions->max <= 0) {
                    return false;
                }

                if ($weight > 7) {
                    return false;
                }
            }
        }

        return true;
    }
}
