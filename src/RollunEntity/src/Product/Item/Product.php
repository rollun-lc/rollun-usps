<?php
declare(strict_types=1);

namespace rollun\Entity\Product\Item;

use rollun\Entity\Product\Dimensions\DimensionsInterface;

/**
 * Class Product
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Product extends AbstractItem
{
    /**
     * @var float
     */
    public $weight;

    /**
     *
     * @var DimensionsInterface
     */
    public $dimensions;

    /**
     * AbstractItem constructor.
     *
     * @param DimensionsInterface $dimensions
     * @param float               $weight
     */
    public function __construct(DimensionsInterface $dimensions, $weight)
    {
        $this->dimensions = $dimensions;
        $this->weight = $weight;
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @inheritDoc
     */
    public function getDimensionsList(): array
    {
        return [['dimensions' => $this->dimensions, 'quantity' => 1]];
    }

    /**
     * @inheritDoc
     */
    public function getVolume(): int
    {
        return (int)round($this->dimensions->getDimensionsRecord()['Volume']);
    }
}
