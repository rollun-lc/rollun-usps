<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Dimensions;

use rollun\Entity\Product\Dimensions\DimensionsInterface;

//https://pe.usps.com/text/dmm300/201.htm#ep1097220
class Rectangular implements DimensionsInterface
{

    public $max;
    public $mid;
    public $min;

    public function __construct($max, $mid, $min)
    {
        $dim = compact('max', 'mid', 'min');
        rsort($dim, SORT_NUMERIC);
        [$this->max, $this->mid, $this->min] = $dim;
    }

    /** @noinspection MagicMethodsValidityInspection */
    public function __set(string $name, float $value)
    {
        //re-calculate min/max/mid when changes
        if (in_array($name, ['max', 'mid', 'min'])) {
            $this->$name = $value;
            $dim = [$this->max, $this->mid, $this->min];
            rsort($dim, SORT_NUMERIC);
            [$this->max, $this->mid, $this->min] = $dim;
        }
    }

    public function getDimensionsRecord($flags = 0)
    {
        return [
            'Type' => 'Rectangular',
            'Length' => $this->max,
            'Width' => $this->mid,
            'Height' => $this->min,
            'Girth' => $this->mid * 2 + $this->min * 2,
            'Volume' => $this->max * $this->mid * $this->min,
            //'Quantity' => 1
        ];
    }
}
