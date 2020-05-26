<?php
declare(strict_types=1);

namespace rollun\HowToBuy\Supplier;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\HowToBuy\Api\DataStore\Shipping\BestShipping;

/**
 * Class Slt
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Slt extends AbstractSupplier
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
                'id'       => 'Root-SLT-DS',
                'type'     => self::TYPE_DS,
                'name'     => self::NAME_DS,
                'priority' => 8
            ],
        ];

    /**
     * @inheritDoc
     */
    public function isInStock(string $rollunId): bool
    {
        $response = BestShipping::httpSend("api/datastore/SltInventoryCacheDataStore?eq(rollun_id,$rollunId)&limit(20,0)");
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
        return 'SLT';
    }

    /**
     * @inheritDoc
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        return true;
    }
}
