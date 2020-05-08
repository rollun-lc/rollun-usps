<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Container;

use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Container\ContainerAbstract;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;

class Box extends ContainerAbstract
{

    const TYPE_BOX = 'Box';

    public $max;
    public $mid;
    public $min;

    public function __construct($max, $mid, $min)
    {
        $dim = compact('max', 'mid', 'min');
        rsort($dim, SORT_NUMERIC);
        [$this->max, $this->mid, $this->min] = $dim;
    }

    protected function canFitProduct(ItemInterface $item): bool
    {
        $dimensionsList = $item->getDimensionsList();
        $dimensions = $dimensionsList[0]['dimensions'];
        return

            $this->max >= $dimensions->max &&
            $this->mid >= $dimensions->mid &&
            $this->min >= $dimensions->min;
    }

    /**
     * @inheritDoc
     */
    protected function canFitProductPack(ItemInterface $item): bool
    {
        $packDimensions = $this->getPackDimensions($item);

        return $this->max >= $packDimensions['max'] && $this->mid >= $packDimensions['mid'] && $this->min >= $packDimensions['min'];
    }

    public function getType(): string
    {
        return static::TYPE_BOX;
    }

    /**
     * @param ItemInterface $item
     *
     * @return array
     */
    public function getPackDimensions(ItemInterface $item): array
    {
        $dimensions = $item->getDimensionsList()[0]['dimensions'];

        $packDimension = ['max' => $dimensions->max, 'mid' => $dimensions->mid, 'min' => $dimensions->min];

        $quantity = $item->quantity;

        while ($quantity > 1) {
            $min = 99999;
            foreach ($packDimension as $key => $value) {
                $tmpMin = $packDimension[$key] + $dimensions->$key;
                if ($tmpMin < $min) {
                    $min = $tmpMin;
                    $side = $key;
                }
            }
            $packDimension[$side] = $packDimension[$side] + $dimensions->$side;
            $quantity--;
        }

        rsort($packDimension, SORT_NUMERIC);

        return ['max' => $packDimension[0], 'mid' => $packDimension[1], 'min' => $packDimension[2]];
    }
}
