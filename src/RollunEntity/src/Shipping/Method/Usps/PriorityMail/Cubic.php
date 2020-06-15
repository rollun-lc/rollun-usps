<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Product\Item\Product;
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
     * Click_N_Shipp => ['id', 'Click_N_Shipp', 'USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width', 'Length', 'Height', 'Weight']
     *
     * 1) The cubic pricing for your USPS package is measured by calculating: Length x Width x Height / 1728 = Cubic Feet.
     * 2) No dimension may exceed 18 inches to be eligible for Cubic Pricing.
     */
    const USPS_BOXES
        = [
            ['PM-Cubic', 'Priority Mail', 'Cubic', '', 'VARIABLE', 18, 18, 18, 70],
        ];

    /**
     * Costs, got from https://pe.usps.com/text/dmm300/Notice123.htm#_c094
     */
    const USPS_COSTS
        = [
            [0.10, 7.02, 7.02, 7.35, 7.56, 7.80, 7.98, 8.15, 8.42, 11.40],
            [0.20, 7.46, 7.46, 7.80, 8.02, 8.71, 9.00, 9.22, 9.56, 13.15],
            [0.30, 8.04, 8.04, 8.26, 8.55, 9.65, 10.98, 11.58, 12.29, 19.12],
            [0.40, 8.21, 8.21, 8.57, 8.93, 10.31, 12.78, 14.02, 16.02, 24.28],
            [0.50, 8.34, 8.34, 8.84, 9.42, 11.15, 14.98, 16.89, 19.24, 29.88],
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
