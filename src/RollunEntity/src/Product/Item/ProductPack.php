<?php
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

/**
 * Class ProductPack
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class ProductPack extends AbstractItem
{
    /**
     * @var Product
     */
    public $product;

    /**
     * @var int
     */
    public $quantity;

    /**
     * ProductPack constructor.
     *
     * @param Product $product
     * @param int     $quantity
     */
    public function __construct(Product $product, $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        return $this->product->getWeight() * $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function getDimensionsList(): array
    {
        return [['dimensions' => $this->product->dimensions, 'quantity' => $this->quantity]];
    }

    /**
     * @inheritDoc
     */
    public function getVolume(): int
    {
        return $this->product->getVolume() * $this->quantity;
    }
}
