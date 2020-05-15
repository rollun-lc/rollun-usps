<?php
declare(strict_types=1);

namespace rollun\Entity\Supplier;

use rollun\Entity\Product\Item\ItemInterface;
use service\Entity\Api\DataStore\Shipping\BestShipping;

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
     * Product title (for defining airAllowed)
     *
     * @var string
     */
    protected $productTitle = 'part_description';

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
        $response = BestShipping::httpSend("api/datastore/PartsUnlimitedInventoryCacheDataStore?eq(rollun_id,$rollunId)&limit(20,0)");
        if (empty($response[0])) {
            return false;
        }

        $this->inventory = $response[0];

        return !empty($this->inventory['s_quantity']);
    }

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod, bool $isAirAllowed = true): bool
    {
        /**
         * For all usps methods
         */
        $parts = explode('-Usps-', $shippingMethod);
        if (isset($parts[1])) {
            if (!$isAirAllowed) {
                return false;
            }

            if ($item->getWeight() > 10) {
                return false;
            }

            if ((float)$item->getAttribute('your_dealer_price') > 100) {
                return false;
            }

            if (empty($item->getAttribute('nc_availability'))) {
                return false;
            }

            if ($parts[1] === 'FtCls-Package' && $item->getWeight() > 0.9) {
                return false;
            }

            if ($parts[1] === 'PM-FR-Env' && $item->getWeight() > 5) {
                return false;
            }
        }

        return true;
    }
}
