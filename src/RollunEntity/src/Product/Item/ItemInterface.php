<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

//use rollun\datastore\DataStore\Memory as MemoryDataStore;

interface ItemInterface
{

    const KEYS_DIMENSIONS_LIST = [
        'dimensions',
        'quantity'
    ];

    public function getWeight();

    //[[dimensions=>dimensionsObject, 'quantity' =>1], [dimensions...]]
    public function getDimensionsList(): array;

    /**
     *
     * @return int Volume in cubic foots
     */
    public function getVolume(): int;
}
