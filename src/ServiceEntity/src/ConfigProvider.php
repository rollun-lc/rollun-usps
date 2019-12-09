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
use service\Entity\Handler\LoggerHandler;
use service\Entity\Handler\MegaplanHandler;
use service\Entity\Handler\Shipping\BestShippingHandler;

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
//            'dataStore' => [
//                'all-price' => [
//                    'class' => UspsRates::class,
//                ],
//                'rm-prodno-price' => [
//                    'class' => RmatvProdNoUspsRate::class,
//                    RmatvProdNoUspsRateAbstractFactory::KEY_DS_RM_FTP_PRICE => 'rm-ftp-price'
//                ],
//                'rm-ftp-price' => [
//                    'class' => CsvBase::class,
//                    'filename' => 'data' . DIRECTORY_SEPARATOR . 'rm-ftp.csv',
//                    'delimiter' => ','
//                ],
//            ]
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
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
                MegaplanHandler::class => MegaplanHandler::class,


//                Handler\HomePageHandler::class => Handler\HomePageHandler::class,
//                Handler\BestPriceHandler::class => Handler\BestPriceHandler::class,
//                Handler\AllPriceHandler::class => Handler\AllPriceHandler::class,
//                'all-price' => UspsRates::class
            ],
        ];
    }
}
