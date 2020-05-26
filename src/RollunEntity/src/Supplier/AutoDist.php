<?php
declare(strict_types=1);

namespace rollun\Entity\Supplier;

use rollun\Entity\Product\Item\ItemInterface;
use service\Entity\Api\DataStore\Shipping\BestShipping;

/**
 * Class AutoDist
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AutoDist extends AbstractSupplier
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
                'id'       => 'Root-AU-DS',
                'type'     => self::TYPE_DS,
                'name'     => self::NAME_DS,
                'priority' => 9
            ],
        ];

    /**
     * @inheritDoc
     */
    public function isInStock(string $rollunId): bool
    {
        $response = BestShipping::httpSend("api/datastore/AutodistInventoryCacheDataStore?eq(rollun_id,$rollunId)&limit(20,0)");
        if (empty($response[0])) {
            return false;
        }

        $this->inventory = $response[0];

        return $this->inventory['avail'] != 'N';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Autodist';
    }

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        return true;
    }
}
