<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\Entity\Shipping\Method\Factory;

use Interop\Container\ContainerInterface;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\DbTable;
use rollun\Entity\Shipping\Method\FixedPrice;
use rollun\Entity\Shipping\Method\Factory\ShippingMethodAbstractFactory;

/**
 * The configuration can contain:
 * <code>
 *  'ShippingMethod' => [
 *      'Priority Mail Medium Flat Rate Box 2' => [
 *          'class' => rollun\Entity\Shipping\Method\FixedPrice::class,
 *          'shortName' = 'FrMb2'
 *          'price' => 29 //$
 *          'containerService' = 'ContainerServiceName'
 *      ]
 *  ]
 * </code>
 */
class FixedPriceAbstractFactory extends ShippingMethodAbstractFactory
{

    const KEY_CONTAINER_SERVICE = 'containerService';

    protected static $KEY_IN_CREATE = 0;
    protected static $KEY_SHIPPING_METHOD_CLASS = FixedPrice::class;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DbTable
     * @throws DataStoreException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($this::$KEY_IN_CREATE) {
            throw new DataStoreException("Create will be called without pre call canCreate method");
        }

        $this::$KEY_IN_CREATE = 1;

        $config = $container->get('config');
        $serviceConfig = $config[self::KEY_SHIPPING_METHOD][$requestedName];
        $requestedClassName = $serviceConfig[self::KEY_CLASS];
        $shortName = $serviceConfig[self::KEY_SHORT_NAME];
        $price = $serviceConfig[self::KEY_PRICE];
        $maxWeight = $serviceConfig[self::KEY_MAX_WEIGHT];

        $containerServiceName = $serviceConfig[self::KEY_CONTAINER_SERVICE];
        if (!$container->has($containerServiceName)) {
            throw new \RuntimeException("Container $containerServiceName do not exisr");
        }
        $containerService = $container->get($containerServiceName);


        $this::$KEY_IN_CREATE = 0;

        return new $requestedClassName($containerService, $shortName, $maxWeight, $price);
    }
}
