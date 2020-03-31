<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Factory;

use Interop\Container\ContainerInterface;
use rollun\Entity\Shipping\Method\LevelBasedShippingMethod;

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
    const KEY_LEVELS = 'levels';

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return !empty($container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName][self::KEY_CLASS])
            && is_a($container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName][self::KEY_CLASS], LevelBasedShippingMethod::class, true);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // get config
        $config = $container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName];

        return new $config[self::KEY_CLASS]($requestedName, isset($config[self::KEY_LEVELS]) ? $config[self::KEY_LEVELS] : null);
    }
}
