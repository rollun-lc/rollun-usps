<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\Usps;

use rollun\Usps\Datastore\RmatvProdNoUspsRate;
use PHPUnit\Framework\TestCase;

class Packager extends TestCase
{

    public function setUp()
    {

    }

    public function stringsRowProvider()
    {
        return[
            [[1, 5, 7], 1, [7, 5, 1]],
            [[1, 5, 7], 2, [7, 5, 2]],
//            [[1, 5, 7], 3, [7, 5, 3]],
            [[1, 5, 7], 4, [7, 5, 4]],
//            [[1, 5, 7], 5, [7, 5, 5]],
//            [[1, 5, 7], 6, [7, 6, 5]],
            [[1, 5, 7], 8, [8, 7, 5]],
//            [[1, 5, 7], 9, [9, 7, 5]],
//            [[1, 5, 7], 10, [10, 7, 5]],
            [[1, 5, 7], 11, [10, 8, 7]],
            [[1, 5, 7], 12, [10, 8, 7]],
            [[3, 5, 7], 6, [12, 10, 7]],
        ];
    }

    /**
     * @dataProvider stringsRowProvider
     */
    public function testProcess($oneItemDimensions, $qty, $expectedArray)
    {
        global $container;
        $object = $container->get('rm-prodno-price');

        var_dump($oneItemDimensions);
        $expectedResultArray['Width'] = $expectedArray[0];
        $expectedResultArray['Length'] = $expectedArray[1];
        $expectedResultArray['Height'] = $expectedArray[2];
        $this->assertEquals(
                $expectedResultArray, $object->getPackDimensions($oneItemDimensions, $qty)
        );
    }

}
