<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Factory;

use Interop\Container\ContainerInterface;
use rollun\Entity\Shipping\Method\Usps\PriorityMailAvailable;
use rollun\Entity\Shipping\Method\Usps\PriorityMailCovid19;

/**
 * Class UspsPriorityMailCovid19AbstractFactory
 *
 * @author r.ratsun <r.ratsun.rollun@gmail.com>
 */
class UspsPriorityMailCovid19AbstractFactory extends ShippingMethodAbstractFactory
{
    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return !empty($container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName][self::KEY_CLASS])
            && is_a($container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName][self::KEY_CLASS], PriorityMailCovid19::class, true);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // get config
        $config = $container->get('config')[self::KEY_SHIPPING_METHOD][$requestedName];

        return new $config[self::KEY_CLASS]($requestedName);
    }
}
