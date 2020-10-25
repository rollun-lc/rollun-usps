<?php


namespace rollun\Entity\Product\Container;


use rollun\Entity\Product\Item\ItemInterface;

class FirstClassPackage extends ContainerAbstract
{
    const TYPE_PACKAGE = 'FirstClassPackage';

    public $max;
    public $mid;
    public $min;
    /**
     * @var float
     */
    private $weight;

    public function __construct($max, $mid, $min, $weight = 0.009)
    {
        $dim = compact('max', 'mid', 'min');
        rsort($dim, SORT_NUMERIC);
        [$this->max, $this->mid, $this->min] = $dim;
        $this->weight = $weight;
    }

    public function getContainerWeight(): float
    {
        return $this->weight;
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

    public function getType(): string
    {
        return static::TYPE_PACKAGE;
    }
}
