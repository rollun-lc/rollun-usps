<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class Cubic
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Cubic extends ShippingsAbstract
{
    /**
     * @var bool
     */
    protected $canShipDangerous = false;

    /**
     * Click_N_Shipp => ['id', 'Click_N_Shipp', 'USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width', 'Length', 'Height', 'Weight']
     *
     * 1) The cubic pricing for your USPS package is measured by calculating: Length x Width x Height / 1728 = Cubic Feet.
     * 2) No dimension may exceed 18 inches to be eligible for Cubic Pricing.
     */
    const USPS_BOXES
        = [
            ['PM-Cubic', 'Priority Mail Cubic', 'Cubic', '', 'CUBIC PARCELS', 18, 18, 18, 20],
        ];

    /**
     * Costs, got from https://pe.usps.com/text/dmm300/Notice123.htm#_c094
     */
    const USPS_COSTS
        = [
            [0.10, 7.79, 7.91, 8.12, 8.37, 8.7, 9.54, 10.07, 10.85, 20.42],
            [0.20, 8.23, 8.31, 8.45, 8.74, 9.39, 11.29, 11.97, 13.17, 27.59],
            [0.30, 8.42, 8.58, 8.86, 9.25, 10.23, 13.74, 15.48, 18.45, 37.43],
            [0.40, 8.55, 8.76, 9.16, 9.83, 12.38, 16.41, 19.31, 22.48, 46.99],
            [0.50, 8.74, 8.95, 9.3, 10.27, 13.89, 18.32, 22.97, 26.76, 56.29],
        ];

    /**
     * @var bool
     */
    protected $hasDefinedCost = true;

    /**
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        if ($this->canBeShipped($shippingRequest)) {
            // prepare cubic feet
            $cubicFeet = $this->getCubicFeet($shippingRequest);

            foreach (self::USPS_COSTS as $row) {
                if ($row[0] >= $cubicFeet) {
                    // get zone
                    $zone = $this->getZone($shippingRequest->getOriginationZipCode(), $shippingRequest->getDestinationZipCode());
                    return $row[$zone];
                }
            }
        }

        return 'Can not be shipped';
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return float
     */
    protected function getCubicFeet(ShippingRequest $shippingRequest): float
    {
        return $shippingRequest->item->getVolume() / 1728;
    }
}
