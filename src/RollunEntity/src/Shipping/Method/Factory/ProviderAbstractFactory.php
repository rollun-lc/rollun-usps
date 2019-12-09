<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\Entity\Shipping\Method\Factory;

use Interop\Container\ContainerInterface;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\DbTable;
use rollun\Entity\Shipping\Method\ShippingMethodProvider;
use rollun\Entity\Shipping\Method\Factory\ShippingMethodAbstractFactory;

/**
 * The configuration can contain:
 * <code>
 *  'ShippingMethod' => [
 *      'Usps' => [
 *          'class' => ShippingMethodProvider::class,
 *          'shortName' => 'USPS',
 *          'shippingMethodList' => [
 *          'shippingMethod1',
 *          'shippingMethodSubProvider',
 *          'shippingMethod2',
 *      ]
 *  ]
 * </code>
 */
class ProviderAbstractFactory extends ShippingMethodAbstractFactory
{

    const KEY_LIST = 'shippingMethodList';

    protected static $KEY_IN_CREATE = 0;
    protected static $KEY_SHIPPING_METHOD_CLASS = ShippingMethodProvider::class;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DbTable
     * @throws DataStoreException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $this::$KEY_IN_CREATE = 1;

        $config = $container->get('config');
        $serviceConfig = $config[self::KEY_SHIPPING_METHOD][$requestedName];

        $requestedClassName = $serviceConfig[self::KEY_CLASS];
        $shortName = $serviceConfig[self::KEY_SHORT_NAME];
        $shippingMethodNameList = $serviceConfig[self::KEY_LIST];
        $shippingMethodList = [];
        foreach ($shippingMethodNameList as $shippingMethodName) {
            $shippingMethodList[] = $container->get($shippingMethodName);
        }

        $this::$KEY_IN_CREATE = 0;

        return new $requestedClassName($shortName, $shippingMethodList);
    }
}
