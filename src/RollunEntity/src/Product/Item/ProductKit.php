<?php
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

/**
 * Class ProductKit
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class ProductKit extends AbstractItem
{
    /**
     *
     * @var array [$productPack1, $productPack2, ...]
     */
    public $items = [];

    /**
     * ProductKit constructor.
     *
     * @param array $items
     */
    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        $weight = 0;
        foreach ($this->items as $item) {
            /* @var $item ItemInterface */
            $weight = $weight + $item->getWeight();
        }

        return $weight;
    }

    /**
     * @inheritDoc
     */
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
     * @inheritDoc
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
