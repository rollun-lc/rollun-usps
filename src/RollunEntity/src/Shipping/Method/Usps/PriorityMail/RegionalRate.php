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
     * @var bool
     */
    protected $canShipDangerous = false;

    /**
     * Click_N_Shipp => ['ShortName', 'Click_N_Shipp', 'USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width', 'Length', 'Height', 'Weight']
     */
    const USPS_BOXES
        = [
            ['PM-RR-BoxA1', 'Priority Mail Regional Rate Box A', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX A', 10, 7, 4.75, 15],
            ['PM-RR-BoxA2', 'Priority Mail Regional Rate Box A', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX A', 10.9375, 12.8125, 2.375, 15],
            ['PM-RR-BoxB1', 'Priority Mail Regional Rate Box B', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX B', 12, 10.25, 5, 20],
            ['PM-RR-BoxB2', 'Priority Mail Regional Rate Box B', 'PRIORITY COMMERCIAL', '', 'REGIONAL RATE BOX B', 15.875, 14.375, 2.875, 20],
        ];

    /**
     * Regional costs, got from https://pe.usps.com/text/dmm300/Notice123.htm#_c091
     */
    const USPS_BOXES_COSTS
        = [
            [7.83, 7.83, 8.04, 8.34, 9.01, 10.89, 11.63, 12.64, 23.37],
            [8.23, 8.23, 8.64, 9.56, 12.36, 17.50, 20.10, 22.90, 42.98],
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
