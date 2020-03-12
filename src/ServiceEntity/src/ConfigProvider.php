<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace service\Entity;

use rollun\Entity\Product\Container\Factory\BoxAbstractFactory;
use rollun\Entity\Shipping\Method\Factory\FixedPriceAbstractFactory;
use rollun\Entity\Shipping\Method\Factory\ProviderAbstractFactory;
use rollun\Entity\Shipping\Method\Usps\UspsProvider;
use service\Entity\Api\DataStore\Shipping\AllCosts;
use service\Entity\Handler\LoggerHandler;
use service\Entity\Handler\MegaplanHandler;
use service\Entity\Handler\Shipping\BestShippingHandler;
use service\Entity\Rollun\Shipping\Method\Provider\RmPrepCenter;
use service\Entity\Rollun\Shipping\Method\Provider\Root as RootProvider;


/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{

    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'aliases' => [
                RootProvider::class => 'Root'
            ],
            'ShippingMethod' => [
                'RmPrepCntr' => [
                    'class' => RmPrepCenter::class,
                    'shortName' => 'RmPrepCntr',
                    'shippingMethodList' => [
                        'Usps'
                    ]
                ],
                'Root' => [
                    'class' => RootProvider::class,
                    'shortName' => 'Root',
                    'shippingMethodList' => [
                        'RmPrepCntr'
                    ]
                ],
            ],
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                RootProvider::class => 'Root'
            ],
            'abstract_factories' => [
                //RmatvProdNoUspsRateAbstractFactory::class
                BoxAbstractFactory::class,
                FixedPriceAbstractFactory::class,
                ProviderAbstractFactory::class
            ],
            'invokables' => [
                BestShippingHandler::class => BestShippingHandler::class,
                LoggerHandler::class => LoggerHandler::class,
                'Usps' => UspsProvider::class,
                'shipping-all-coosts' => AllCosts::class
            ],
        ];
    }
}
