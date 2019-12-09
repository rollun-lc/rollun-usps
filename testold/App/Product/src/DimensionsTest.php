<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\Product;

use PHPUnit\Framework\TestCase;
use rollun\Product\Dimensions;

class DimensionsTest extends TestCase
{

    public function setUp()
    {

    }

    public function sProvider()
    {
        return[
            [1, 3, 2, [3, 2, 1]],
        ];
    }

    /**
     * @dataProvider sProvider
     */
    public function testProcess($max, $mid, $min, $expected)
    {
        //global $container;
        $dimensions = new Dimensions($max, $mid, $min);

        $dim = $dimensions->getDimensions();
        $this->assertTrue(
                $dim['Width'] > $dim['Length'] && $dim['Length'] > $dim['Height']
        );
    }

}
