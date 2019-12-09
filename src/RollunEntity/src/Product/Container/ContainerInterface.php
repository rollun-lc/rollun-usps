<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Container;

use rollun\Entity\Product\Item\ItemInterface;

interface ContainerInterface
{

    public function getType(): string;

    public function canFit(ItemInterface $item): bool;

    public function getContainerWeight(): float;
}
