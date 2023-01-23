<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\ParcelSelect;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Parcel Select — Ground
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Ground extends ShippingsAbstract
{
    /**
     * Click_N_Shipp => ['id', 'Click_N_Shipp', 'USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width', 'Length', 'Height', 'Weight']
     *
     * 1. IMpb Noncompliant Fee or eVS Unmanifested Fee $0.20 per piece. @todo add it in the future
     * 2. For parcels that measure in combined length and girth more than 108 inches but not more than 130 inches, use oversized prices, regardless of weight, based on the applicable zone.
     * 3. Parcels that exceed one cubic foot (1,728 cubic inches) are charged based on the actual weight or the dimensional weight, whichever is greater (as calculated in DMM 253.1.3).
     */
    const USPS_BOXES
        = [
            ['PS-Ground', 'Parcel Select Ground', 'PARCEL SELECT GROUND', '', 'RECTANGULAR', 130, 130, 130, 70],
        ];

    /**
     * Defined costs, got from https://pe.usps.com/text/dmm300/Notice123.htm#_c139 for Parcel Select — Ground
     */
    const USPS_ZONE_COSTS
        = [
            /* lb, zone 1, zone 2, zone 3, zone 4, zone 5, zone 6, zone 7, zone 8, zone 9*/
            [1, 6.99, 7.22, 7.53, 7.75, 7.87, 8.09, 8.61, 9.03, 9.03],
            [2, 7.13, 7.5, 7.64, 7.95, 8.22, 9.58, 10.21, 10.85, 10.85],
            [3, 7.19, 7.56, 7.74, 8.14, 8.8, 11.19, 11.63, 12.25, 12.25],
            [4, 7.29, 7.66, 7.98, 8.65, 10.11, 11.7, 12.54, 13.17, 13.17],
            [5, 7.4, 7.78, 8.14, 8.91, 10.31, 12.46, 13.19, 13.98, 13.98],
            [6, 7.58, 7.89, 8.5, 9.4, 12.27, 12.77, 13.55, 14.32, 14.32],
            [7, 8.02, 8.34, 9.27, 9.84, 12.65, 13.09, 14.02, 15.03, 15.03],
            [8, 8.16, 8.49, 9.84, 11.2, 12.97, 13.58, 14.5, 15.62, 15.62],
            [9, 8.9, 9.26, 10.28, 11.77, 13.17, 13.96, 15.2, 16.57, 16.57],
            [10, 9.35, 9.72, 10.8, 11.94, 13.59, 14.38, 16.22, 17.83, 17.83],
            [11, 11.02, 11.53, 11.79, 12.97, 14.05, 15.06, 17.86, 19.27, 19.27],
            [12, 11.55, 11.95, 12.39, 13.21, 14.33, 15.7, 18.66, 20.26, 20.26],
            [13, 11.86, 12.23, 12.81, 13.55, 14.82, 16.26, 20.05, 21.69, 21.69],
            [14, 12.24, 12.54, 13.1, 13.62, 15.08, 17.29, 21.41, 23.21, 23.21],
            [15, 12.43, 12.62, 13.58, 14.06, 15.85, 18.33, 22.19, 24.75, 24.75],
            [16, 12.74, 12.88, 13.91, 14.2, 16.03, 19.01, 23.19, 25.5, 25.5],
            [17, 13.04, 13.9, 14.41, 14.64, 16.74, 19.91, 24.68, 26.34, 26.34],
            [18, 13.11, 13.97, 14.52, 14.8, 17.46, 20.84, 25.25, 27.97, 27.97],
            [19, 13.46, 14.34, 15.22, 15.45, 18.65, 21.52, 26.19, 29.17, 29.17],
            [20, 13.69, 14.59, 15.54, 15.85, 19.16, 22.33, 27.23, 30.58, 30.58],
            [21, 14.8, 15.91, 16.86, 17.67, 21.56, 25.79, 31.31, 35.17, 35.17],
            [22, 15.9, 17.09, 18.29, 19.7, 24.26, 29.79, 36.01, 40.44, 40.44],
            [23, 17.1, 18.38, 19.85, 21.97, 27.29, 34.41, 41.41, 46.51, 46.51],
            [24, 18.38, 19.76, 21.53, 24.49, 30.7, 39.74, 47.62, 53.48, 53.48],
            [25, 19.76, 21.24, 23.36, 27.31, 34.54, 45.9, 54.76, 61.51, 61.51],
            [26, 22.59, 24.28, 27.79, 35.48, 45.76, 57.74, 67.83, 77.39, 77.39],
            [27, 23.96, 25.76, 29.07, 37.67, 49.93, 58.54, 69.55, 80.27, 80.27],
            [28, 24.71, 26.56, 29.46, 38.76, 51.25, 59.36, 71.22, 83.48, 83.48],
            [29, 25.48, 27.39, 29.77, 39.82, 51.94, 60.38, 72.9, 85.84, 85.84],
            [30, 26.25, 28.22, 30.21, 40.76, 52.66, 62.11, 74.55, 87.68, 87.68],
            [31, 27.01, 29.04, 30.51, 41.41, 53.34, 63.03, 76.24, 89.72, 89.72],
            [32, 27.32, 29.37, 31.17, 42.11, 53.97, 63.87, 77.93, 91.35, 91.35],
            [33, 27.75, 29.83, 32.05, 43.17, 54.69, 65.14, 79.58, 93.24, 93.24],
            [34, 28.01, 30.11, 32.9, 44.28, 55.89, 66.73, 81.26, 95.08, 95.08],
            [35, 28.33, 30.45, 33.68, 44.92, 57.09, 68.55, 82.93, 96.55, 96.55],
            [36, 28.69, 30.84, 34.68, 45.52, 58.34, 70.32, 84.08, 98.28, 98.28],
            [37, 28.99, 31.16, 35.33, 46.18, 59.39, 72.2, 85.19, 99.96, 99.96],
            [38, 29.28, 31.48, 36.2, 46.77, 60.59, 74.25, 86.18, 101.64, 101.64],
            [39, 29.57, 31.79, 37.07, 47.31, 61.86, 76.04, 88.52, 103.22, 103.22],
            [40, 29.88, 32.12, 37.85, 47.93, 63.16, 77.28, 90.54, 104.69, 104.69],
            [41, 30.21, 32.48, 38.49, 48.45, 63.72, 78.61, 92.52, 106.37, 106.37],
            [42, 30.44, 32.72, 38.79, 48.88, 64.81, 80.02, 93.81, 107.73, 107.73],
            [43, 30.8, 33.11, 39.08, 49.32, 65.89, 81.97, 95, 108.89, 108.89],
            [44, 31.02, 33.35, 39.36, 49.75, 66.96, 83.3, 96.16, 110.46, 110.46],
            [45, 31.22, 33.56, 39.65, 50.2, 68.05, 84.24, 97.22, 111.83, 111.83],
            [46, 31.5, 33.86, 39.94, 50.64, 69.13, 85.2, 98.29, 113.14, 113.14],
            [47, 31.73, 34.11, 40.23, 51.07, 70.21, 86.1, 99.44, 114.5, 114.5],
            [48, 32, 34.4, 40.52, 51.51, 71.28, 87.22, 100.41, 115.66, 115.66],
            [49, 32.26, 34.68, 40.79, 51.95, 72.37, 88.44, 101.48, 116.81, 116.81],
            [50, 32.39, 34.82, 41.08, 52.4, 73.46, 89.7, 102.8, 118.07, 118.07],
            [51, 32.88, 35.35, 41.37, 52.8, 74.72, 90.95, 104.3, 119.23, 119.23],
            [52, 33.37, 35.87, 41.66, 53.25, 75.25, 91.84, 105.9, 120.59, 120.59],
            [53, 34, 36.55, 41.94, 53.69, 75.87, 92.63, 107.67, 122.17, 122.17],
            [54, 34.5, 37.09, 42.24, 54.12, 76.53, 93.3, 109.24, 123.95, 123.95],
            [55, 35.05, 37.68, 42.51, 54.56, 77.02, 94.09, 111.01, 125.58, 125.58],
            [56, 35.54, 38.21, 42.81, 55, 77.61, 94.72, 112.56, 126.89, 126.89],
            [57, 36.11, 38.82, 43.09, 55.44, 78.07, 95.46, 113.3, 127.79, 127.79],
            [58, 36.67, 39.42, 43.38, 55.87, 78.57, 96.03, 114.35, 128.89, 128.89],
            [59, 37.2, 39.99, 43.67, 56.3, 79.05, 96.58, 115.08, 129.83, 129.83],
            [60, 37.67, 40.5, 43.95, 56.73, 79.5, 97.07, 115.82, 130.67, 130.67],
            [61, 38.29, 41.16, 44.23, 57.17, 79.89, 97.62, 117.18, 132.51, 132.51],
            [62, 38.77, 41.68, 44.52, 57.6, 80.25, 98.08, 118.61, 134.72, 134.72],
            [63, 39.48, 42.44, 44.81, 58.05, 80.68, 98.65, 119.19, 136.92, 136.92],
            [64, 39.83, 42.82, 45.09, 58.49, 81.04, 99.1, 119.74, 139.07, 139.07],
            [65, 40.41, 43.44, 45.38, 58.94, 81.29, 99.39, 120.35, 141.17, 141.17],
            [66, 40.95, 44.02, 45.68, 59.36, 81.66, 99.9, 120.72, 143.43, 143.43],
            [67, 41.57, 44.69, 45.96, 60.38, 81.95, 100.22, 121.21, 145.27, 145.27],
            [68, 42.06, 45.21, 46.24, 61.14, 82.18, 101.5, 121.85, 146.84, 146.84],
            [69, 42.64, 45.84, 46.54, 61.93, 82.43, 102.74, 122.43, 148.42, 148.42],
            [70, 43.09, 46.32, 46.82, 62.92, 82.69, 104, 122.88, 150.1, 150.1],
            [1000, 84, 90.3, 106.79, 129.62, 152.15, 174.93, 197.66, 220.5, 220.5],
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
            // prepare weight
            $weight = $this->getWeight($shippingRequest);

            foreach (self::USPS_ZONE_COSTS as $row) {
                if ($row[0] >= $weight) {
                    // get zone
                    $zone = $this->getZone($shippingRequest->getOriginationZipCode(), $shippingRequest->getDestinationZipCode());

                    // temporary increase cost for winter holidays
                    return $this->increaseCost($row[0], $zone, $row[$zone]);
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
    protected function getWeight(ShippingRequest $shippingRequest): float
    {
        // For parcels that measure in combined length and girth more than 108 inches but not more than 130 inches, use oversized prices
        if ($this->getCombinedLengthAndGirth($shippingRequest->item) > 108) {
            return 999;
        }

        $volume = $shippingRequest->item->getVolume();

        $weight = $shippingRequest->item->getWeight();

        // Parcels that exceed one cubic foot (1,728 cubic inches) are charged based on the actual weight or the dimensional weight, whichever is greater (as calculated in DMM 253.1.3).
        if ($volume > 1728) {
            $lbs = $volume / 166;
            if ($lbs > $weight) {
                $weight = $lbs;
            }
        }

        return $weight;
    }

    /**
     * @param ItemInterface $item
     *
     * @return float
     */
    protected function getCombinedLengthAndGirth(ItemInterface $item): float
    {
        /** @var array $dimensions */
        $dimensions = $item->getDimensionsList()[0]['dimensions']->getDimensionsRecord();

        $result = $dimensions['Girth'] + $dimensions['Length'];

        return $result;
    }

    private function increaseCost(float $planWeight, int $zone, float $cost): float
    {
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
