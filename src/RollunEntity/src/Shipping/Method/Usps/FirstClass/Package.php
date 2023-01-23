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
            [1, 3.59, 3.64, 3.66, 3.75, 3.81, 3.90, 3.97, 4.13, 4.13],
            [2, 3.59, 3.64, 3.66, 3.75, 3.81, 3.90, 3.97, 4.13, 4.13],
            [3, 3.59, 3.64, 3.66, 3.75, 3.81, 3.90, 3.97, 4.13, 4.13],
            [4, 3.59, 3.64, 3.66, 3.75, 3.81, 3.90, 3.97, 4.13, 4.13],
            [5, 3.99, 4.06, 4.09, 4.15, 4.20, 4.24, 4.31, 4.44, 4.44],
            [6, 3.99, 4.06, 4.09, 4.15, 4.20, 4.24, 4.31, 4.44, 4.44],
            [7, 3.99, 4.06, 4.09, 4.15, 4.20, 4.24, 4.31, 4.44, 4.44],
            [8, 3.99, 4.06, 4.09, 4.15, 4.20, 4.24, 4.31, 4.44, 4.44],
            [9, 4.62, 4.69, 4.74, 4.81, 4.88, 5.04, 5.17, 5.33, 5.33],
            [10, 4.62, 4.69, 4.74, 4.81, 4.88, 5.04, 5.17, 5.33, 5.33,],
            [11, 4.62, 4.69, 4.74, 4.81, 4.88, 5.04, 5.17, 5.33, 5.33,],
            [12, 4.62, 4.69, 4.74, 4.81, 4.88, 5.04, 5.17, 5.33, 5.33,],
            [13, 5.85, 5.93, 6.00, 6.07, 6.22, 6.44, 6.60, 6.78, 6.78,],
            [14, 5.85, 5.93, 6.00, 6.07, 6.22, 6.44, 6.60, 6.78, 6.78,],
            [15, 5.85, 5.93, 6.00, 6.07, 6.22, 6.44, 6.60, 6.78, 6.78,],
            [15.999, 5.85, 5.93, 6.00, 6.07, 6.22, 6.44, 6.60, 6.78, 6.78,],
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

                    // temporary increase cost for winter holidays
                    return $this->increaseCost($row[0], $zone, $row[$zone]);
                }
            }
        }

        return 'Can not be shipped';
    }

    private function increaseCost(float $planWeight, int $zone, float $cost): float
    {
        // convert to lb
        $planWeight = $planWeight / 16;

        if ($planWeight <= 10) {
            if ($zone <= 4) {
                $cost += 0.25;
            } else {
                $cost += 0.4;
            }
        } elseif ($planWeight <= 25) {
            if ($zone <= 4) {
                $cost += 0.75;
            } else {
                $cost += 1.6;
            }
        } elseif ($planWeight <= 70) {
            if ($zone <= 4) {
                $cost += 3;
            } else {
                $cost += 5.5;
            }
        }

        return $cost;
    }
}
