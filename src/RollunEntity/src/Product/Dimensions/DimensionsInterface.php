<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Dimensions;

//https://pe.usps.com/text/dmm300/201.htm#ep1097220
interface DimensionsInterface
{

    const COLUMNS = [
        'Type', //Rectangular, Tube, Bagful
        'Length',
        'Width',
        'Height',
        'Girth',
        'Volume', //Volume of max parallelepiped
            //'Quantity' // =1
    ];

    public function getDimensionsRecord($flags = 0);

//    /**
//     *
//     * @param int $flags
//     * @return array ['max' => $length, 'mid' => $width, 'min' => $height]
//     *
//     */
//    public function getMaxDimensions($flags = 0);
}
