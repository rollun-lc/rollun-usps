<?php
declare(strict_types=1);

namespace service\Entity;

use rollun\Entity\Product\Container\Factory\BoxAbstractFactory;
use rollun\Entity\Shipping\Method\DropShip\PuDropShip;
use rollun\Entity\Shipping\Method\DropShip\RmDropShip;
use rollun\Entity\Shipping\Method\Factory\FixedPriceAbstractFactory;
use rollun\Entity\Shipping\Method\Factory\ProviderAbstractFactory;
use rollun\Entity\Shipping\Method\Provider\PickUp\RmPickUp;
use rollun\Entity\Shipping\Method\Provider\PickUp\PuPickUp;
use rollun\Entity\Shipping\Method\Usps\UspsProvider;
use service\Entity\Api\DataStore\Shipping\AllCosts;
use service\Entity\Handler\LoggerHandler;
use service\Entity\Handler\MegaplanHandler;
use service\Entity\Handler\Shipping\BestShippingHandler;
use service\Entity\Rollun\Shipping\Method\Provider\Root as RootProvider;


/**
 * Class ConfigProvider
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     */
    public function __invoke(): array
    {
        return [
            'dependencies'   => $this->getDependencies(),
            'ShippingMethod' => $this->getShippingMethods(),
            'Container'      => $this->getShippingContainers()
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'aliases'            => [
                RootProvider::class => 'Root',
            ],
            'abstract_factories' => [
                BoxAbstractFactory::class,
                FixedPriceAbstractFactory::class,
                ProviderAbstractFactory::class
            ],
            'invokables'         => [
                BestShippingHandler::class => BestShippingHandler::class,
                LoggerHandler::class       => LoggerHandler::class,
                'Usps'                     => UspsProvider::class,
                'shipping-all-costs'       => AllCosts::class,
                'RM-DS'                    => RmDropShip::class,
                'PU-DS'                    => PuDropShip::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getShippingMethods(): array
    {
        return [
            'Root'      => [
                'class'              => RootProvider::class,
                'shortName'          => 'Root',
                'shippingMethodList' => [
                    'RM-DS',
                    'RM-PickUp',
                    'PU-DS',
                    'PU-PickUp'
                ]
            ],
            'RM-DS'     => [
                'class'     => RmDropShip::class,
                'shortName' => 'RM-DS'
            ],
            'RM-PickUp' => [
                'class'              => RmPickUp::class,
                'shortName'          => 'RM-PickUp',
                'shippingMethodList' => [
                    'Usps'
                ]
            ],
            'PU-DS'     => [
                'class'     => RmDropShip::class,
                'shortName' => 'PU-DS'
            ],
            'PU-PickUp' => [
                'class'              => PuPickUp::class,
                'shortName'          => 'PU-PickUp',
                'shippingMethodList' => [
                    'Usps'
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function getShippingContainers(): array
    {
        return [];
    }
}
