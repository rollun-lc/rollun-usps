<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Container;

use rollun\Entity\Product\Container\ContainerInterface;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;

abstract class ContainerAbstract implements ContainerInterface
{

    public function canFit(ItemInterface $item): bool
    {
        $dimensionsList = $item->getDimensionsList();


        if (count($dimensionsList) === 1 && $dimensionsList[0]['quantity'] === 1) {
            return $this->canFitProduct($item);
        }
        if (count($dimensionsList) === 1 || $dimensionsList[0]['quantity'] > 1) {
            return $this->canFitProductPack($item);
        }
        return $this->canFitProductKit($item);
    }

    public function getContainerWeight(): float
    {
        return 0.0;
    }

    protected function canFitProduct(ItemInterface $item): bool
    {
        return false;
    }

    protected function canFitProductPack(ItemInterface $item): bool
    {
        return false;
    }

    protected function canFitProductKit(ItemInterface $item): bool
    {
        return false;
    }
}
