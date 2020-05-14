<?php
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

/**
 * Interface ItemInterface
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
interface ItemInterface
{
    const KEYS_DIMENSIONS_LIST = ['dimensions', 'quantity'];

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @return array
     */
    public function getDimensionsList(): array;

    /**
     * @return int Volume in cubic foots
     */
    public function getVolume(): int;
}
