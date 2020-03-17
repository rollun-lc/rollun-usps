<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class RegionalRate
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class RegionalRate extends ShippingsAbstract
{
    /**
     * Click_N_Shipp => ['ShortName','Click_N_Shipp','USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width','Length',Weight,'Height']
     */
    const USPS_BOXES
        = [
            ['PM-RR-BoxA1', 'Priority Mail Regional Rate Box A', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX A', 10, 7, 4.75, 70],
            ['PM-RR-BoxA2', 'Priority Mail Regional Rate Box A', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX A', 10.9375, 12.8125, 2.375, 70],
            ['PM-RR-BoxB1', 'Priority Mail Regional Rate Box B', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX B', 12, 10.25, 5, 70],
            ['PM-RR-BoxB2', 'Priority Mail Regional Rate Box B', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX B', 15.875, 14.375, 2.875, 70],
        ];

    /**
     * Regional costs
     */
    const USPS_BOXES_COSTS
        = [
            [7.68, 7.68, 7.92, 8.21, 8.92, 10.42, 11.13, 12.10, 18.69],
            [8.07, 8.07, 8.51, 9.42, 11.53, 16.72, 19.21, 21.89, 34.38],
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

            if (in_array($this->shortName, ['PM-RR-BoxA1', 'PM-RR-BoxA2'])) {
                $costs = self::USPS_BOXES_COSTS[0];
            } elseif (in_array($this->shortName, ['PM-RR-BoxB1', 'PM-RR-BoxB2'])) {
                $costs = self::USPS_BOXES_COSTS[1];
            } else {
                $costs = [];
            }

            if (!empty($costs)) {
                $zone = $this->getZone($shippingRequest->getOriginationZipCode(), $shippingRequest->getDestinationZipCode()) - 1;
                if (isset($costs[$zone])) {
                    return $costs[$zone];
                }
            }
        }

        return 'Can not be shipped';
    }
}
