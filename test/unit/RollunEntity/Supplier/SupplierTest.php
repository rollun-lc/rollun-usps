<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Supplier;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;
use rollun\Entity\Supplier\AutoDist;
use rollun\Entity\Supplier\PartsUnlimited;
use rollun\Entity\Supplier\RockyMountain;
use rollun\Entity\Supplier\Slt;

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
            [PartsUnlimited::class, $this->createProductPack(8, 9, 2, 3, 50), '91730', 'Root-PU-PickUp-Usps-PM-FR-Env'],
            [PartsUnlimited::class, $this->createProductPack(2, 2, 1, 0.5, 89), '91730', 'Root-PU-PickUp-Usps-FtCls-Package'],
            [PartsUnlimited::class, $this->createProductPack(2, 2, 1, 0.5, 101), '91730', 'Root-PU-DS'],
            [PartsUnlimited::class, $this->createProductPack(2, 2, 1, 0.5, 55, 1, false), '91730', 'Root-PU-DS'],
            [RockyMountain::class, $this->createProductPack(8, 9, 2, 3, 50), '80000', 'Root-RM-PickUp-Usps-PM-FR-Env'],
            [RockyMountain::class, $this->createProductPack(2, 2, 1, 0.5, 89), '91730', 'Root-RM-PickUp-Usps-FtCls-Package'],
            [RockyMountain::class, $this->createProductPack(2, 2, 1, 0.5, 101, 0), '91730', 'Root-RM-DS-Ontrack'],
            [Slt::class, $this->createProductPack(2, 2, 1, 0.5, 101), '91730', 'Root-SLT-DS'],
            [AutoDist::class, $this->createProductPack(2, 2, 1, 0.5, 101), '91730', 'Root-AU-DS'],
            [AutoDist::class, $this->createProductPack(2, 2, 1, 71, 101), '91730', null],
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

    /**
     * @param float $max
     * @param float $mid
     * @param float $min
     * @param float $weight
     * @param float $price
     * @param int   $quantity
     * @param bool  $airAllowed
     *
     * @return ProductPack
     */
    protected function createProductPack(float $max, float $mid, float $min, float $weight, float $price, int $quantity = 1, bool $airAllowed = true): ProductPack
    {
        $productPack = new ProductPack(new Product(new Rectangular($max, $mid, $min), $weight), $quantity);
        $productPack->addAttribute('price', $price);
        $productPack->addAttribute('airAllowed', $airAllowed);

        return $productPack;
    }
}
