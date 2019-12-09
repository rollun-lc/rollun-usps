<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\Entity\Shipping\Method\Factory;

use Interop\Container\ContainerInterface;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\Entity\Shipping\Method\ShippingMethodInterface;

/**
 * Class DataStoreAbstractFactory
 * @package rollun\datastore\DataStore\Factory
 */
abstract class ShippingMethodAbstractFactory extends AbstractFactoryAbstract
{

    const KEY_SHIPPING_METHOD = 'ShippingMethod';
    const KEY_SHORT_NAME = 'shortName';
    const KEY_PRICE = 'price';
    const KEY_MAX_WEIGHT = 'maxWeight';

    protected static $KEY_SHIPPING_METHOD_CLASS = ShippingMethodInterface::class;
    protected static $KEY_IN_CANCREATE = 0;
    protected static $KEY_IN_CREATE = 0;

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (static::$KEY_IN_CANCREATE > 20 || static::$KEY_IN_CREATE > 20) {
            return false;
        }

        static::$KEY_IN_CANCREATE = static::$KEY_IN_CANCREATE + 1;

        $config = $container->get('config');

        if (!isset($config[static::KEY_SHIPPING_METHOD][$requestedName][static::KEY_CLASS])) {
            $result = false;
        } else {
            $requestedClassName = $config[static::KEY_SHIPPING_METHOD][$requestedName][static::KEY_CLASS];
            $result = is_a($requestedClassName, static::$KEY_SHIPPING_METHOD_CLASS, true);
        }

        $this::$KEY_IN_CANCREATE = static::$KEY_IN_CANCREATE - 1;

        return $result;
    }
}
