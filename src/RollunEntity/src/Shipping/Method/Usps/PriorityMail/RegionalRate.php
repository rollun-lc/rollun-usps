<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Product\Container\ContainerInterface as ProductContainerInterface;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\Method\FixedPrice;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract as UspsShippingsAbstract;

class RegionalRate extends UspsShippingsAbstract
{

    public $atributes = [];

    /**
     * Click_N_Shipp => ['ShortName','Click_N_Shipp','USPS_API_Service',
     * 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width','Length',Weight,'Height',Price]
     */
    const USPS_BOXES = [
        ['PM-RR-BoxA1', 'Priority Mail Regional Rate Box A',
            'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX A', 10, 7, 4.75, 70],
        ['PM-RR-BoxA2', 'Priority Mail Regional Rate Box A',
            'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX A', 10.9375, 12.8125, 2.375, 70],
        ['PM-RR-BoxB1', 'Priority Mail Regional Rate Box B',
            'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX B', 12, 10.25, 5, 70],
        ['PM-RR-BoxB2', 'Priority Mail Regional Rate Box B',
            'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX B', 15.875, 14.375, 2.875, 70],
    ];

    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return parent::canBeShipped($shippingRequest);
    }
}
