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
            [0.10, 7.57, 7.83, 8.06, 8.87, 9.62, 8.87, 9.89, 10.27, 16.89],
            [0.20, 8.39, 8.71, 8.96, 9.75, 10.43, 9.68, 10.68, 11.06, 17.84],
            [0.30, 9.02, 9.21, 9.53, 10.72, 12.54, 11.79, 13.20, 14.00, 25.44],
            [0.40, 9.18, 9.52, 9.91, 11.35, 14.33, 13.58, 15.64, 17.76, 31.59],
            [0.50, 9.33, 9.83, 10.47, 12.25, 16.77, 16.77, 18.83, 21.33, 39.03],
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
