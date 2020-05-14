<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Supplier;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Supplier\PartsUnlimited;

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
            [PartsUnlimited::class, (new Product(new Rectangular(8, 9, 2), 3))->addAttribute('price', 50)->addAttribute('airAllowed', true), '91730', 'Root-PU-PickUp-Usps-PM-FR-Env']
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
