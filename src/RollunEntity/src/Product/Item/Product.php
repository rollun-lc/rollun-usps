<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Dimensions\DimensionsInterface;

class Product implements ItemInterface
{

    public $weight;

    /**
     *
     * @var DimensionsInterface
     */
    public $dimensions;

    public function __construct(DimensionsInterface $dimensions, $weight)
    {
        /* @var $dimensions DimensionsInterface */
        $this->dimensions = $dimensions;
        $this->weight = $weight;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getDimensionsList(): array
    {
        return[['dimensions' => $this->dimensions, 'quantity' => 1]];
    }

    /**
     *
     * @return int Volume in cubic foots
     */
    public function getVolume(): int
    {
        return (int)round($this->dimensions->getDimensionsRecord()['Volume']);
    }
}
