<?php
declare(strict_types=1);

namespace rollun\HowToBuy;

use rollun\HowToBuy\Shipping\Method\Factory\UspsPriorityMailCovid19AbstractFactory;
use rollun\HowToBuy\Shipping\Method\Usps\PriorityMailCovid19;
use rollun\HowToBuy\Supplier\AutoDist;
use rollun\HowToBuy\Supplier\PartsUnlimited;
use rollun\HowToBuy\Supplier\RockyMountain;
use rollun\HowToBuy\Supplier\Slt;
use rollun\HowToBuy\Api\DataStore\Shipping\BestShipping;

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
                'best-shipping' => BestShipping::class,
            ],
            'abstract_factories' => [
                UspsPriorityMailCovid19AbstractFactory::class,
            ],
            'invokables'         => [
                BestShipping::class   => BestShipping::class,
                PartsUnlimited::class => PartsUnlimited::class,
                RockyMountain::class  => RockyMountain::class,
                Slt::class            => Slt::class,
                AutoDist::class       => AutoDist::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getShippingMethods(): array
    {
        return [
            'RM-PickUp'                   => [
                'shippingMethodList' => [
                    'Usps-PM-FR-Env-COVID19',
                    'Usps-PM-FR-LegalEnv-COVID19',
                    'Usps-PM-FR-Pad-Env-COVID19',
                    'Usps-PM-COVID19',
                ]
            ],
            'Usps-PM-FR-Env-COVID19'      => [
                'class' => PriorityMailCovid19::class
            ],
            'Usps-PM-FR-LegalEnv-COVID19' => [
                'class' => PriorityMailCovid19::class
            ],
            'Usps-PM-FR-Pad-Env-COVID19'  => [
                'class' => PriorityMailCovid19::class
            ],
            'Usps-PM-COVID19'             => [
                'class' => PriorityMailCovid19::class
            ],
        ];
    }
}
