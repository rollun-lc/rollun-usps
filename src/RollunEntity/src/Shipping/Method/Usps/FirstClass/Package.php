<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\FirstClass;

use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class Package
 *
 * Unless Priority Mail Express is used, Priority Mail prices are required for a mailpiece that weighs
 * more than 13 ounces when the mailpiece contains matter that must be mailed as First-Class Mail (see 233.3.0).
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Package extends ShippingsAbstract
{
    /**
     * @var bool
     */
    protected $canShipDangerous = false;

    /**
     * Click_N_Shipp => ['ShortName', 'Click_N_Shipp', 'USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width', 'Length', 'Height', 'Weight']
     */
    const USPS_BOXES = [['FtCls-Package', 'First-Class Package Service', 'FIRST CLASS COMMERCIAL', 'PACKAGE SERVICE', '', 22, 18, 15, 0.999]];

    /**
     * Defined costs, got from https://pe.usps.com/text/dmm300/Notice123.htm#_c107
     */
    const USPS_PACKAGE_COSTS
        = [
            /* oz, zone 1, zone 2, zone 3, zone 4, zone 5, zone 6, zone 7, zone 8, zone 9*/
            [1, 3.01, 3.03, 3.06, 3.12, 3.22, 3.35, 3.49, 3.49],
            [2, 3.01, 3.03, 3.06, 3.12, 3.22, 3.35, 3.49, 3.49],
            [3, 3.01, 3.03, 3.06, 3.12, 3.22, 3.35, 3.49, 3.49],
            [4, 3.01, 3.03, 3.06, 3.12, 3.22, 3.35, 3.49, 3.49],
            [5, 3.46, 3.49, 3.51, 3.57, 3.58, 3.69, 3.85, 3.85],
            [6, 3.46, 3.49, 3.51, 3.57, 3.58, 3.69, 3.85, 3.85],
            [7, 3.46, 3.49, 3.51, 3.57, 3.58, 3.69, 3.85, 3.85],
            [8, 3.46, 3.49, 3.51, 3.57, 3.58, 3.69, 3.85, 3.85],
            [9, 4.04, 4.09, 4.12, 4.20, 4.38, 4.53, 4.68, 4.68],
            [10, 4.04, 4.09, 4.12, 4.20, 4.38, 4.53, 4.68, 4.68],
            [11, 4.04, 4.09, 4.12, 4.20, 4.38, 4.53, 4.68, 4.68],
            [12, 4.04, 4.09, 4.12, 4.20, 4.38, 4.53, 4.68, 4.68],
            [13, 5.19, 5.23, 5.27, 5.42, 5.66, 5.81, 5.98, 5.98],
            [14, 5.19, 5.23, 5.27, 5.42, 5.66, 5.81, 5.98, 5.98],
            [15, 5.19, 5.23, 5.27, 5.42, 5.66, 5.81, 5.98, 5.98],
            [15.999, 5.19, 5.23, 5.27, 5.42, 5.66, 5.81, 5.98, 5.98],
        ];

    /**
     * @var bool
     */
    protected $hasDefinedCost = true;

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        if ($this->canBeShipped($shippingRequest)) {
            // prepare oz
            $oz = $shippingRequest->item->getWeight() * 16;

            foreach (self::USPS_PACKAGE_COSTS as $row) {
                if ($row[0] >= $oz) {
                    // get zone
                    $zone = $this->getZone($shippingRequest->getOriginationZipCode(), $shippingRequest->getDestinationZipCode());

                    return $row[$zone];
                }
            }
        }

        return 'Can not be shipped';
    }
}
