<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\Entity\Product\Container\Factory;

use Interop\Container\ContainerInterface;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\Entity\Product\Container\ContainerInterface as ProductContainerInterface;

/**
 * Class DataStoreAbstractFactory
 * @package rollun\datastore\DataStore\Factory
 */
abstract class ContainerAbstractFactory extends AbstractFactoryAbstract
{

    const KEY_CONTAINER = 'Container';

    protected static $KEY_CONTAINER_CLASS = ProductContainerInterface::class;
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
        if (static::$KEY_IN_CANCREATE || static::$KEY_IN_CREATE) {
            return false;
        }

        static::$KEY_IN_CANCREATE = 1;

        $config = $container->get('config');

        if (!isset($config[static::KEY_CONTAINER][$requestedName][static::KEY_CLASS])) {
            $result = false;
        } else {
            $requestedClassName = $config[static::KEY_CONTAINER][$requestedName][static::KEY_CLASS];
            $result = is_a($requestedClassName, static::$KEY_CONTAINER_CLASS, true);
        }

        $this::$KEY_IN_CANCREATE = 0;

        return $result;
    }
}
