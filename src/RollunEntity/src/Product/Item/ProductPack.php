<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;

class ProductPack implements ItemInterface
{

    public $product;
    public $quantity;

    public function __construct(Product $product, $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getWeight()
    {
        return $this->product->getWeight() * $this->quantity;
    }

    public function getDimensionsList(): array
    {
        return [['dimensions' => $this->product->dimensions, 'quantity' => $this->quantity]];
    }

    /**
     *
     * @return int Volume in cubic foots
     */
    public function getVolume(): int
    {
        return $this->product->getVolume() * $this->quantity;
    }
}
