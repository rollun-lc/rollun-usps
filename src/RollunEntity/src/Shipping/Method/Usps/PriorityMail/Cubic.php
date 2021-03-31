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
            [0.10, 7.32, 7.32, 7.59, 7.81, 8.12, 8.87, 9.14, 9.52, 16.14],
            [0.20, 8.14, 8.14, 8.46, 8.71, 9.00, 9.68, 9.93, 10.31, 17.09],
            [0.30, 8.77, 8.77, 8.96, 9.28, 9.97, 11.79, 12.45, 13.25, 24.69],
            [0.40, 8.93, 8.93, 9.27, 9.66, 10.60, 13.58, 14.89, 17.01, 30.84],
            [0.50, 9.08, 9.08, 9.58, 10.22, 11.50, 16.02, 18.08, 20.58, 38.28],
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
