<?php
declare(strict_types=1);

namespace rollun\HowToBuy\Supplier;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\HowToBuy\Api\DataStore\Shipping\BestShipping;

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
    const PICKUP_COURIER = 'Martha';

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
//            @todo temporally ignore pickup methods fo current supplier
//            [
//                'id'     => 'Root-PU-PickUp-Usps-FtCls-Package',
//                'priority' => 1,
//                'name'     => null,
//                'courier'  => self::PICKUP_COURIER
//            ],
//            [
//                'id'     => 'Root-PU-PickUp-Usps-PM-FR-Env',
//                'priority' => 3,
//                'name'     => null,
//                'courier'  => self::PICKUP_COURIER
//            ],
            [
                'id'       => 'Root-PU-DS',
                'type'     => self::TYPE_DS,
                'name'     => self::NAME_DS,
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
    public function getName(): string
    {
        return 'Parts Unlimited';
    }

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function isUspsValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        $parts = explode('-Usps-', $shippingMethod);
        if (isset($parts[1])) {
            if ($item->getWeight() > 10) {
                return false;
            }

            if ((float)$item->getAttribute('your_dealer_price') > 100) {
                return false;
            }

            if (empty($item->getAttribute('nc_availability'))) {
                return false;
            }
        }

        return parent::isUspsValid($item, $zipDestination, $shippingMethod);
    }
}