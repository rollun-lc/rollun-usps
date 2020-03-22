<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Factory;

use Interop\Container\ContainerInterface;

/**
 * Class LevelBasedShippingAbstractFactory
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class LevelBasedShippingAbstractFactory extends ShippingMethodAbstractFactory
{
    const KEY = 'levels';

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return !empty($container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName])
            && key_exists(self::KEY, $container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName]);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // get config
        $config = $container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName];

        // get class name
        $className = $config[self::KEY_CLASS];

        return new $className($requestedName, $config['levels']);
    }
}
