<?php
declare(strict_types=1);

namespace service\Entity;

use rollun\Entity\Product\Container\Factory\BoxAbstractFactory;
use rollun\Entity\Shipping\Method\DropShip\AuDropShip;
use rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19\AuDropShipCovid19AtvUtvTires;
use rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19\AuDropShipCovid19AtvUtvWheels;
use rollun\Entity\Shipping\Method\DropShip\AuDropShipCovid19\AuDropShipCovid19MotorcycleTires;
use rollun\Entity\Shipping\Method\DropShip\PuDropShip;
use rollun\Entity\Shipping\Method\DropShip\RmDropShip;
use rollun\Entity\Shipping\Method\DropShip\RmOntrackDropShip;
use rollun\Entity\Shipping\Method\DropShip\SltDropShip;
use rollun\Entity\Shipping\Method\DropShip\TrDropShip;
use rollun\Entity\Shipping\Method\DropShip\WpsDropShip;
use rollun\Entity\Shipping\Method\Factory\FixedPriceAbstractFactory;
use rollun\Entity\Shipping\Method\Factory\LevelBasedShippingAbstractFactory;
use rollun\Entity\Shipping\Method\Factory\ProviderAbstractFactory;
use rollun\Entity\Shipping\Method\Provider\PickUp\RmPickUp;
use rollun\Entity\Shipping\Method\Provider\PickUp\PuPickUp;
use rollun\Entity\Shipping\Method\ShippingMethodProvider;
use rollun\Entity\Shipping\Method\Usps\UspsProvider;
use service\Entity\Api\DataStore\Shipping\AllCosts;
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
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'aliases'            => [
                RootProvider::class   => 'Root',
                'Usps'                => UspsProvider::class,
                'shipping-all-coosts' => AllCosts::class,
                'shipping-all-costs'  => AllCosts::class,
            ],
            'abstract_factories' => [
                BoxAbstractFactory::class,
                FixedPriceAbstractFactory::class,
                ProviderAbstractFactory::class,
                LevelBasedShippingAbstractFactory::class
            ],
            'invokables'         => [
                UspsProvider::class => UspsProvider::class,
                AllCosts::class     => AllCosts::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getShippingMethods(): array
    {
        return [
            'Root'             => [
                'class'              => RootProvider::class,
                'shortName'          => 'Root',
                'shippingMethodList' => [
                    'RM-PickUp',
                    'RM-DS',
                    'RM-DS-Ontrack',
                    'PU-PickUp',
                    'PU-DS',
                    'WPS-DS',
                    'TR-DS',
                    'SLT-DS',
                    'AU-DS',
                    'AU-DS-COVID19' // @todo remove when covid19 finished
                ]
            ],
            'RM-DS'            => [
                'class' => RmDropShip::class
            ],
            'RM-DS-Ontrack'    => [
                'class' => RmOntrackDropShip::class
            ],
            'RM-PickUp'        => [
                'class'              => RmPickUp::class,
                'shortName'          => 'RM-PickUp',
                'shippingMethodList' => [
                    'Usps',
                ]
            ],
            'PU-DS'            => [
                'class' => PuDropShip::class
            ],
            'PU-PickUp'        => [
                'class'              => PuPickUp::class,
                'shortName'          => 'PU-PickUp',
                'shippingMethodList' => [
                    'Usps'
                ]
            ],
            'WPS-DS'           => [
                'class' => WpsDropShip::class
            ],
            'TR-DS'            => [
                'class' => TrDropShip::class
            ],
            'SLT-DS'           => [
                'class' => SltDropShip::class
            ],
            'AU-DS'            => [
                'class' => AuDropShip::class
            ],
            // @todo remove when covid19 finished
            'AU-DS-COVID19'    => [
                'class'              => ShippingMethodProvider::class,
                'shortName'          => 'AU-DS-COVID19',
                'shippingMethodList' => [
                    'ATV/UTV-TIRES',
                    'ATV/UTV-WHEELS',
                    'MOTORCYCLE-TIRES'
                ]
            ],
            // @todo remove when covid19 finished
            'ATV/UTV-TIRES'    => [
                'class' => AuDropShipCovid19AtvUtvTires::class
            ],
            // @todo remove when covid19 finished
            'ATV/UTV-WHEELS'   => [
                'class' => AuDropShipCovid19AtvUtvWheels::class
            ],
            // @todo remove when covid19 finished
            'MOTORCYCLE-TIRES' => [
                'class' => AuDropShipCovid19MotorcycleTires::class
            ],
        ];
    }
}
