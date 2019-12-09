<?php
/**
 * Created by PhpStorm.
 * User: itprofessor02
 * Date: 26.03.19
 * Time: 18:39
 */

namespace rollun\test\functional\Shipping\Api;

use PHPUnit\Framework\TestCase;
use service\Entity\Api\DataStore\Shipping\AllCosts;
use service\Shipping\Api\ShippingTypeResolver;
use service\Shipping\Api\ShippingTypeResolverStrategy\UspsShipping;
use service\Shipping\Api\ShippingTypeResolverStrategy\InRockyUtahStorage;
use service\Shipping\Api\ShippingTypeResolverStrategyInterface;
use service\Shipping\Client\RockyShippingPriceServiceClientInterface;

class ShippingTypeResolverTest extends TestCase
{
    public function shippingDataProvider()
    {
        return [
            [
                [
                    'part_number' => 'TEST_9090909090_TEST',
                    'utah_quantity' => 2,
                    'destination_zip_code' => '54615',
                    'price' => 40,
                    'qty' => 1,
                    'weight' => 0.395,
                    'width' => 8.75,
                    'height' => 0.5,
                    'length' => 5.5,
                ],
                ShippingTypeResolver::SHIPPING_TYPE_USPS
            ],
            [
                [
                    'part_number' => 'TEST_9090909090_TEST',
                    'utah_quantity' => 0,
                    'destination_zip_code' => '84772',
                    'price' => 40,
                    'qty' => 1,
                    'weight' => 0.1,
                    'width' => 0.5,
                    'height' => 0.1,
                    'length' => 0.1,
                ],
                ShippingTypeResolver::SHIPPING_TYPE_ROCKY_DROPSHIP
            ],
            [
                [
                    'part_number' => 'TEST_9090909090_TEST',
                    'utah_quantity' => 12,
                    'destination_zip_code' => '84111',
                    'price' => 40,
                    'qty' => 1,
                    'weight' => 1.1,
                    'width' => 5,
                    'height' => 0.1,
                    'length' => 0.1,
                ],
                ShippingTypeResolver::SHIPPING_TYPE_ROCKY_DROPSHIP
            ],
            [
                [
                    'part_number' => 'TEST_9090909090_TEST',
                    'utah_quantity' => 12,
                    'destination_zip_code' => '99211',
                    'price' => 15,
                    'qty' => 1,
                    'weight' => 1.1,
                    'width' => 5,
                    'height' => 0.1,
                    'length' => 0.1,
                ],
                ShippingTypeResolver::SHIPPING_TYPE_ROCKY_DROPSHIP
            ],
            [
                [
                    'part_number' => 'TEST_9090909090_TEST',
                    'utah_quantity' => 12,
                    'destination_zip_code' => '74508',
                    'price' => 40,
                    'qty' => 1,
                    'weight' => 8,
                    'width' => 5,
                    'height' => 4,
                    'length' => 3,
                ],
                ShippingTypeResolver::SHIPPING_TYPE_UNKNOWN
            ],
        ];
    }

    /**
     * @dataProvider shippingDataProvider
     * @param $data
     * @param $type
     * @throws \ReflectionException
     */
    public function testShippingResolver($data, $type)
    {
        $object = new ShippingTypeResolver([
            new InRockyUtahStorage(),
            new UspsShipping(new AllCosts()),
        ]);

        ['shipping_type' => $result] = call_user_func($object, $data);
        $this->assertEquals($type, $result);
    }
}
