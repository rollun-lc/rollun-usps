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
            [1, 3.31, 3.31, 3.33, 3.36, 3.42, 3.52, 3.65, 3.79, 3.79],
            [2, 3.31, 3.31, 3.33, 3.36, 3.42, 3.52, 3.65, 3.79, 3.79],
            [3, 3.31, 3.31, 3.33, 3.36, 3.42, 3.52, 3.65, 3.79, 3.79],
            [4, 3.31, 3.31, 3.33, 3.36, 3.42, 3.52, 3.65, 3.79, 3.79],
            [5, 3.76, 3.76, 3.79, 3.81, 3.87, 3.88, 3.99, 4.15, 4.15],
            [6, 3.76, 3.76, 3.79, 3.81, 3.87, 3.88, 3.99, 4.15, 4.15],
            [7, 3.76, 3.76, 3.79, 3.81, 3.87, 3.88, 3.99, 4.15, 4.15],
            [8, 3.76, 3.76, 3.79, 3.81, 3.87, 3.88, 3.99, 4.15, 4.15],
            [9, 4.34, 4.34, 4.39, 4.42, 4.50, 4.68, 4.83, 4.98, 4.98],
            [10, 4.34, 4.34, 4.39, 4.42, 4.50, 4.68, 4.83, 4.98, 4.98,],
            [11, 4.34, 4.34, 4.39, 4.42, 4.50, 4.68, 4.83, 4.98, 4.98,],
            [12, 4.34, 4.34, 4.39, 4.42, 4.50, 4.68, 4.83, 4.98, 4.98,],
            [13, 5.49, 5.49, 5.53, 5.57, 5.72, 5.96, 6.11, 6.28, 6.28,],
            [14, 5.49, 5.49, 5.53, 5.57, 5.72, 5.96, 6.11, 6.28, 6.28,],
            [15, 5.49, 5.49, 5.53, 5.57, 5.72, 5.96, 6.11, 6.28, 6.28,],
            [15.999, 5.49,5.49, 5.53, 5.57, 5.72, 5.96, 6.11, 6.28, 6.28,],
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

    private function increaseCost(int $planWeight, int $zone, float $cost): float
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
