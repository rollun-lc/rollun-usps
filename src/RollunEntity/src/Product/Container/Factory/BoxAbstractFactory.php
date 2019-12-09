<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\Entity\Product\Container\Factory;

use Interop\Container\ContainerInterface;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\DbTable;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Product\Container\Factory\ContainerAbstractFactory;

/**
 * The configuration can contain:
 * <code>
 *  'Container' => [
 *      'Flat Rate Box 2' => [
 *          'class' => Box::class,
 *          'Length' => 12,
 *          'Width' => 10, // in inches
 *          'Height' => 3
 *      ]
 *  ]
 * </code>
 */
class BoxAbstractFactory extends ContainerAbstractFactory
{

    const KEY_MAX = 'Length';
    const KEY_MID = 'Width';
    const KEY_MIN = 'Height';

    protected static $KEY_IN_CREATE = 0;

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
        $serviceConfig = $config[self::KEY_CONTAINER][$requestedName];
        $requestedClassName = $serviceConfig[self::KEY_CLASS];

        $max = $serviceConfig[self::KEY_MAX];
        $mid = $serviceConfig[self::KEY_MID];
        $min = $serviceConfig[self::KEY_MIN];


        $this::$KEY_IN_CREATE = 0;

        return new $requestedClassName($max, $mid, $min);
    }
}
