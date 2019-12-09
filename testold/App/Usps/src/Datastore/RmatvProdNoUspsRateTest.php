<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\Usps\Datastore;

use PHPUnit\Framework\TestCase;

class RmatvProdNoUspsRateTest extends TestCase
{

    public function setUp()
    {

    }

    public function sProvider()
    {
        return[
            [1, 1],
        ];
    }

    /**
     * @dataProvider sProvider
     */
    public function testProcess($one, $expectedy)
    {
        global $container;

        $this->assertEquals(
                $one, $expectedy
        );
    }

}
