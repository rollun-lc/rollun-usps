<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

use rollun\callback\Middleware\CallablePluginManagerFactory;
use rollun\Entity\Shipping\Method\Usps\UspsProvider;
use service\Entity\Rollun\Shipping\Method\Provider\RmPrepCenter;
use service\Entity\Rollun\Shipping\Method\Provider\Root as RootProvider;
use service\Entity\Api\DataStore\Shipping\AllCosts;
use service\Shipping\Api\MegaplanDealRegister;

return [
    'dependencies' => [
        'invokables' => [
            "MegaplanDealRegister" => MegaplanDealRegister::class,
            'Usps' => UspsProvider::class,
            'shipping-all-coosts' => AllCosts::class
        ],
    ],
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
    CallablePluginManagerFactory::KEY_INTERRUPTERS => [
        'invokables' => [
            "MegaplanDealRegister" => MegaplanDealRegister::class,
        ],
    ],
];
