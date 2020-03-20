<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Factory;

use Interop\Container\ContainerInterface;

/**
 * Class DropShipAbstractFactory
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class DropShipAbstractFactory extends ShippingMethodAbstractFactory
{
    const KEY_DS = 'isDropShip';

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return !empty($container->get('config')[static::KEY_SHIPPING_METHOD][$requestedName][self::KEY_DS]);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // get class name
        $className = $container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName][self::KEY_CLASS];

        return new $className($requestedName);
    }
}
