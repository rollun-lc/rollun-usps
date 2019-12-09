<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Container;

use rollun\Entity\Product\Container\ContainerAbstract;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;

class Tube extends ContainerAbstract
{

    const TYPE_TUBE = 'Tube';

    public $max;

    private $lengthAndGirthCombined;

    public function __construct($max, $mid, $min = 0)
    {
        $this->lengthAndGirthCombined = $max;
    }

    public function getType(): string
    {
        return static::TYPE_TUBE;
    }

    protected function canFitProduct(ItemInterface $item): bool
    {
        if (!($item instanceof Product)) {
            return false;
        }

        $dimensionsList = $item->getDimensionsList();
        $dimensions = $dimensionsList[0]['dimensions'];

        $girth = $dimensions->getDimensionsRecord()['Girth'];
        $length = $dimensions->getDimensionsRecord()['Length'];

        return $length + $girth < $this->lengthAndGirthCombined;
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
