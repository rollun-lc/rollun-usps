<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Product\Dimensions;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Dimensions\Rectangular;

class RectangularTest extends TestCase
{

    public function testProcessFullZip()
    {

        $rectangular = new Rectangular(10, 30, 5);
        $expected = [
            'Type' => 'Rectangular',
            'Length' => 30,
            'Width' => 10,
            'Height' => 5,
            'Girth' => 30,
            'Volume' => 1500
                //'Quantity' => 1
        ];
        $this->assertEquals(
                $expected, $rectangular->getDimensionsRecord()
        );
    }
}
