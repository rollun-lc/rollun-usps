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
    const PICKUP_COURIER = 'Jeremy';

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
                'id'       => 'Root-RM-PickUp-Usps-FtCls-Package',
                'type'     => self::TYPE_PU,
                'priority' => 1,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-PickUp-Usps-PM-FR-Env-COVID19',
                'type'     => self::TYPE_PU,
                'priority' => 1.5,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-PickUp-Usps-PM-FR-LegalEnv-COVID19',
                'type'     => self::TYPE_PU,
                'priority' => 1.5,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-PickUp-Usps-PM-FR-Pad-Env-COVID19',
                'type'     => self::TYPE_PU,
                'priority' => 1.5,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-PickUp-Usps-PM-COVID19',
                'type'     => self::TYPE_PU,
                'priority' => 1.5,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-DS-Ontrack',
                'type'     => self::TYPE_DS,
                'priority' => 2
            ],
            [
                'id'       => 'Root-RM-PickUp-Usps-PM-FR-Env',
                'type'     => self::TYPE_PU,
                'priority' => 3,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-PickUp-Usps-PM-FR-LegalEnv',
                'type'     => self::TYPE_PU,
                'priority' => 5,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-PickUp-Usps-PM-FR-Pad-Env',
                'type'     => self::TYPE_PU,
                'priority' => 6,
                'courier'  => self::PICKUP_COURIER
            ],
            [
                'id'       => 'Root-RM-DS',
                'type'     => self::TYPE_DS,
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
    public function getName(): string
    {
        return 'Rocky Mountain';
    }

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        if ($shippingMethod === 'Root-RM-DS-Ontrack' && empty($item->getAttribute('qty_ut'))) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function isUspsValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        $parts = explode('-Usps-', $shippingMethod);
        if (isset($parts[1])) {
            if ($item->getWeight() > 20) {
                return false;
            }

            if ((float)$item->getAttribute('rmatv_price') > 100) {
                return false;
            }

            if (empty($item->getAttribute('qty_ut'))) {
                return false;
            }
        }

        return parent::isUspsValid($item, $zipDestination, $shippingMethod);
    }
}
