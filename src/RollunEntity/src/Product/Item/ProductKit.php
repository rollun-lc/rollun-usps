<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

use rollun\Entity\Product\Item\ItemInterface;

class ProductKit implements ItemInterface
{

    /**
     *
     * @var array [$productPack1, $productPack2, ...]
     */
    public $items = [];

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function getWeight()
    {
        $weight = 0;
        foreach ($this->items as $item) {
            /* @var $item ItemInterface */
            $weight = $weight + $item->getWeight();
        }
        return $weight;
    }

    public function getDimensionsList(): array
    {
        $dimensionsList = [];
        foreach ($this->items as $item) {
            /* @var $item ItemInterface */
            $dimensionsList = array_merge($dimensionsList, $item->getDimensionsList());
        }
        return $dimensionsList;
    }

    /**
     *
     * @return int Volume in cubic foots
     */
    public function getVolume(): int
    {
        $volume = 0;
        foreach ($this->items as $item) {
            /* @var $item ItemInterface */
            $volume = $volume + $item->getVolume();
        }
        return $volume;
    }
}
