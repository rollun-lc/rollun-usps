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
            [1, 2.74, 2.74, 2.76, 2.78, 2.84, 2.93, 3.05, 3.18, 3.18],
            [2, 2.74, 2.74, 2.76, 2.78, 2.84, 2.93, 3.05, 3.18, 3.18],
            [3, 2.74, 2.74, 2.76, 2.78, 2.84, 2.93, 3.05, 3.18, 3.18],
            [4, 2.74, 2.74, 2.76, 2.78, 2.84, 2.93, 3.05, 3.18, 3.18],
            [5, 3.21, 3.21, 3.23, 3.25, 3.31, 3.39, 3.52, 3.67, 3.67],
            [6, 3.21, 3.21, 3.23, 3.25, 3.31, 3.39, 3.52, 3.67, 3.67],
            [7, 3.21, 3.21, 3.23, 3.25, 3.31, 3.39, 3.52, 3.67, 3.67],
            [8, 3.21, 3.21, 3.23, 3.25, 3.31, 3.39, 3.52, 3.67, 3.67],
            [9, 3.93, 3.93, 3.97, 4.00, 4.08, 4.18, 4.32, 4.46, 4.46],
            [10, 3.93, 3.93, 3.97, 4.00, 4.08, 4.18, 4.32, 4.46, 4.46],
            [11, 3.93, 3.93, 3.97, 4.00, 4.08, 4.18, 4.32, 4.46, 4.46],
            [12, 3.93, 3.93, 3.97, 4.00, 4.08, 4.18, 4.32, 4.46, 4.46],
            [13, 5.04, 5.04, 5.08, 5.12, 5.27, 5.40, 5.54, 5.70, 5.70],
            [14, 5.04, 5.04, 5.08, 5.12, 5.27, 5.40, 5.54, 5.70, 5.70],
            [15, 5.04, 5.04, 5.08, 5.12, 5.27, 5.40, 5.54, 5.70, 5.70],
            [15.99, 5.04, 5.04, 5.08, 5.12, 5.27, 5.40, 5.54, 5.70, 5.70]
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
