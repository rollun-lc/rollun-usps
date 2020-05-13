<?php
declare(strict_types=1);

namespace rollun\Entity\Product\Container;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;

/**
 * Class ContainerAbstract
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class ContainerAbstract implements ContainerInterface
{
    /**
     * @param ItemInterface $item
     *
     * @return bool
     */
    public function canFit(ItemInterface $item): bool
    {
        if ($item instanceof Product) {
            return $this->canFitProduct($item);
        }

        if ($item instanceof ProductPack) {
            return $this->canFitProductPack($item);
        }

        return $this->canFitProductKit($item);
    }

    /**
     * @return float
     */
    public function getContainerWeight(): float
    {
        return 0.0;
    }

    /**
     * @param ItemInterface $item
     *
     * @return bool
     */
    protected function canFitProduct(ItemInterface $item): bool
    {
        return false;
    }

    /**
     * @param ItemInterface $item
     *
     * @return bool
     */
    protected function canFitProductPack(ItemInterface $item): bool
    {
        return false;
    }

    /**
     * @param ItemInterface $item
     *
     * @return bool
     */
    protected function canFitProductKit(ItemInterface $item): bool
    {
        return false;
    }
}
