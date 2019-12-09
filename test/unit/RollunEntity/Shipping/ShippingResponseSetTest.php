<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Shipping\ShippingResponseSet;

class ShippingResponseSetTest extends TestCase
{

    public function testAppend()
    {
        $mergedResponseSet1 = new ShippingResponseSet();
        $mergedResponseSet1->append(['id' => 'RMATV-USPS-FRLG1', 'cost' => 17.89]);
        $this->assertEquals(
                [['id' => 'RMATV-USPS-FRLG1', 'cost' => 17.89]], $mergedResponseSet1->getArrayCopy()
        );

        $mergedResponseSet2 = new ShippingResponseSet();
        $mergedResponseSet2->append(['id' => 'RMATV-DS', 'cost' => 8.95]);

        $rollunProviderResponseSet = new ShippingResponseSet();
        $rollunProviderResponseSet->mergeResponseSet($mergedResponseSet1, 'ROLLUN');
        $rollunProviderResponseSet->mergeResponseSet($mergedResponseSet2, 'ROLLUN');

        $expected = [
            ['id' => 'ROLLUN-RMATV-USPS-FRLG1', 'cost' => 17.89],
            ['id' => 'ROLLUN-RMATV-DS', 'cost' => 8.95]
        ];
        $this->assertEquals(
                $expected, $rollunProviderResponseSet->getArrayCopy()
        );
    }
}
