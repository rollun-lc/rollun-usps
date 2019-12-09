<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\FirstClass;

/**
 * Unless Priority Mail Express is used, Priority Mail prices are required for a mailpiece that weighs
 * more than 13 ounces when the mailpiece contains matter that must be mailed as First-Class Mail (see 233.3.0).
 */
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract as UspsShippingsAbstract;
use rollun\Entity\Product\Item\Product;

class Package extends UspsShippingsAbstract
{

    /**
     * Click_N_Shipp => ['ShortName','Click_N_Shipp','USPS_API_Service',
     * 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width','Length',Weight,'Height',Price]
     */
    const USPS_BOXES = [
        ['FtCls-Package', 'First-Class Package Service',
            'FIRST CLASS COMMERCIAL', 'PACKAGE SERVICE', '', 22, 18, 15, 0.999],
    ];

}
