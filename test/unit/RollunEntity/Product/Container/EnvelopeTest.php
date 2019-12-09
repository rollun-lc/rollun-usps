<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Product\Dimensions;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Container\Envelope;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;

class EnvelopeTest extends TestCase
{

    public function testBoxTrue()
    {

        $box = new Envelope(12.5, 9.5);
        $rectangular = new Rectangular(8, 5, 4);
        $product = new Product($rectangular, 0.5);

        $this->assertEquals(true, $box->canFit($product));
    }

    public function testBoxFalse()
    {

        $box = new Envelope(12.5, 9.5);

        $rectangular = new Rectangular(8, 6, 5);
        $product = new Product($rectangular, 0.5);

        $this->assertEquals(false, $box->canFit($product));
    }


    public function cantFitRectangularDataProvider()
    {
        return [
            [new Envelope(15, 9.5), new Rectangular(8, 7.5, 3.5)],
            [new Envelope(15, 9.5), new Rectangular(4.75, 4.75, 4.75)]

        ];
    }

    /**
     * @dataProvider cantFitRectangularDataProvider
     */
    public function testEnvelopeCantFit(Envelope $box, Rectangular $rectangular)
    {
        //1.370, 8.00/3.50/7.50 --- Legal Flat Rate  ---где это 15" x 9 1/2" Картонный конверт
        $product = new Product($rectangular, 1.370);
        $this->assertEquals(false, $box->canFit($product));
    }


    public function fitRectangularDataProvider()
    {
        return [
            [new Envelope(15, 9.5), new Rectangular(4, 4, 4)],
            [new Envelope(15, 9.5), new Rectangular(4.5, 4.5, 4.5)]
        ];
    }

    /**
     * @dataProvider fitRectangularDataProvider
     * @param Envelope $box
     * @param Rectangular $rectangular
     */
    public function testEnvelopeCanFit(Envelope $box, Rectangular $rectangular): void
    {
        $product = new Product($rectangular, 1.370);
        $this->assertEquals(true, $box->canFit($product));
    }
}
