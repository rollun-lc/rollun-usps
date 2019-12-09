<?php

/**
 * Development-only configuration.
 *
 * Put settings you want enabled when under development mode in this file, and
 * check it into your repository.
 *
 * Developers on your team will then automatically enable them by calling on
 * `composer development-enable`.
 */
declare(strict_types=1);

use rollun\Entity\Product\Container\Box;
use rollun\Entity\Shipping\Method\ShippingMethodProvider;
use rollun\Entity\Product\Container\Factory\BoxAbstractFactory;
use rollun\Entity\Shipping\Method\Factory\FixedPriceAbstractFactory;
use rollun\Entity\Shipping\Method\Factory\ProviderAbstractFactory;

return [
    'dependencies' => [
        'invokables' => [
            BestShippingHandler::class => BestShippingHandler::class
        ],
        'factories' => [
        ],
        'abstract_factories' => [
//RmatvProdNoUspsRateAbstractFactory::class
            BoxAbstractFactory::class,
            FixedPriceAbstractFactory::class,
            ProviderAbstractFactory::class
        ],
        'aliases' => [
        ],
    ],
    'Container' => [
        'Flat Rate Box 2' => [
            'class' => Box::class,
            'Length' => 12,
            'Width' => 10, // in inches
            'Height' => 3
        ],
        'box1' => [
            'class' => Box::class,
            'Length' => 35,
            'Width' => 6, // in inches
            'Height' => 11
        ],
        'box2' => [
            'class' => Box::class,
            'Length' => 10,
            'Width' => 10, // in inches
            'Height' => 10
        ]
    ],
    'ShippingMethod' => [
        'Priority Mail Medium Flat Rate Box 2' => [
            'class' => rollun\Entity\Shipping\Method\FixedPrice::class,
            'shortName' => 'FrMb2',
            'price' => 29, //$
            'maxWeight' => 15, //lbs
            'containerService' => 'Flat Rate Box 2',
        ],
        'UspsTest' => [
            'class' => ShippingMethodProvider::class,
            'shortName' => 'UspsTest',
            'shippingMethodList' => [
                'fixedPrice1',
                'fixedPrice2',
            ]
        ],
        'fixedPrice1' => [
            'class' => rollun\Entity\Shipping\Method\FixedPrice::class,
            'shortName' => 'Md1',
            'price' => 10, //$
            'maxWeight' => 15, //lbs
            'containerService' => 'box1',
        ],
        'fixedPrice2' => [
            'class' => rollun\Entity\Shipping\Method\FixedPrice::class,
            'shortName' => 'Md2',
            'price' => 20, //$
            'maxWeight' => 15, //lbs
            'containerService' => 'box2',
        ]
    ]
];
