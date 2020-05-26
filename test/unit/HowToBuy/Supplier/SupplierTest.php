<?php
declare(strict_types=1);

namespace test\unit\rollun\HowToBuy\Supplier;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;
use rollun\HowToBuy\Supplier\AutoDist;
use rollun\HowToBuy\Supplier\PartsUnlimited;
use rollun\HowToBuy\Supplier\RockyMountain;
use rollun\HowToBuy\Supplier\Slt;

/**
 * Class SupplierTest
 *
 * @author r.ratsun <r.ratsun.rollun@gmail.com>
 */
class SupplierTest extends TestCase
{
    public function getBestShippingMethodDataProvider(): array
    {
        return [
//            [
//                PartsUnlimited::class,
//                (new Product(new Rectangular(9, 8, 2), 3))->setAttributes(['your_dealer_price' => 50, 'nc_availability' => 4, 'isAirAllowed' => true]),
//                '91730',
//                'Root-PU-PickUp-Usps-PM-FR-Env'
//            ],
//            [
//                PartsUnlimited::class,
//                (new Product(new Rectangular(2, 2, 1), 0.5))->setAttributes(['your_dealer_price' => 89, 'nc_availability' => 4, 'isAirAllowed' => true]),
//                '91730',
//                'Root-PU-PickUp-Usps-FtCls-Package'
//            ],
            [
                PartsUnlimited::class,
                (new Product(new Rectangular(2, 2, 1), 0.5))->setAttributes(['your_dealer_price' => 101, 'nc_availability' => 4, 'isAirAllowed' => true]),
                '91730',
                'Root-PU-DS'
            ],
            [
                PartsUnlimited::class,
                (new Product(new Rectangular(2, 2, 1), 0.5))->setAttributes(['your_dealer_price' => 55, 'nc_availability' => 0, 'isAirAllowed' => true]),
                '91730',
                'Root-PU-DS'
            ],
            [
                RockyMountain::class,
                (new Product(new Rectangular(9, 8, 0), 3))->setAttributes(['rmatv_price' => 50, 'qty_ut' => 2, 'isAirAllowed' => true]),
                '80000',
                'Root-RM-PickUp-Usps-PM-FR-Env'
            ],
            [
                RockyMountain::class,
                (new Product(new Rectangular(2, 2, 1), 0.5))->setAttributes(['rmatv_price' => 89, 'qty_ut' => 2, 'isAirAllowed' => true]),
                '91730',
                'Root-RM-PickUp-Usps-FtCls-Package'
            ],
            [
                RockyMountain::class,
                (new Product(new Rectangular(2, 2, 1), 0.5))->setAttributes(['rmatv_price' => 89, 'qty_ut' => 2]),
                '91730',
                'Root-RM-DS-Ontrack'
            ],
            [
                RockyMountain::class,
                (new Product(new Rectangular(2, 2, 1), 0.5))->setAttributes(['rmatv_price' => 101, 'qty_ut' => 2, 'isAirAllowed' => true]),
                '91730',
                'Root-RM-DS-Ontrack'
            ],
            [
                RockyMountain::class,
                (new Product(new Rectangular(2, 2, 1), 0.5))->setAttributes(['rmatv_price' => 101, 'qty_ut' => 0, 'isAirAllowed' => true]),
                '91730',
                'Root-RM-DS'
            ],
            [
                Slt::class,
                new Product(new Rectangular(2, 2, 1), 0.5),
                '91730',
                'Root-SLT-DS'
            ],
            [
                AutoDist::class,
                new Product(new Rectangular(2, 2, 1), 0.5),
                '91730',
                'Root-AU-DS'
            ],
            [
                AutoDist::class,
                new Product(new Rectangular(2, 2, 1), 75),
                '91730',
                null
            ],
        ];
    }

    /**
     * @param string        $supplierClass
     * @param ItemInterface $item
     * @param string        $zipDestination
     * @param mixed         $expected
     *
     * @dataProvider getBestShippingMethodDataProvider
     */
    public function testGetBestShippingMethod(string $supplierClass, ItemInterface $item, string $zipDestination, $expected)
    {
        global $container;

        $actual = $container->get($supplierClass)->getBestShippingMethod($item, $zipDestination);

        $this->assertEquals($expected, empty($actual) ? null : $actual['id']);
    }
}
