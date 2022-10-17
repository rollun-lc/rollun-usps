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
            [1, 7.26, 7.26, 7.56, 7.77, 8.38, 8.59, 8.72, 8.9, 8.9],
            [2, 7.84, 7.84, 8.01, 8.29, 9.3, 10.54, 11.01, 11.54, 11.54],
            [3, 8, 8, 8.35, 8.72, 10.04, 12.55, 13.75, 15.8, 15.8],
            [4, 8.1, 8.1, 8.58, 9.21, 10.83, 14.65, 16.43, 18.31, 18.31],
            [5, 8.2, 8.2, 8.63, 9.53, 12.1, 16.72, 18.94, 21.31, 21.31],
            [6, 8.31, 8.31, 8.67, 9.64, 14.72, 19.24, 22.17, 25.1, 25.1],
            [7, 8.58, 8.58, 9.93, 9.98, 16.78, 21.08, 24.59, 27.83, 27.83],
            [8, 8.64, 8.64, 10.43, 11.8, 18.29, 23.19, 27.16, 31.39, 31.39],
            [9, 9.5, 9.5, 10.83, 12.3, 19.59, 25.27, 29.48, 35.02, 35.02],
            [10, 9.9, 9.9, 11.29, 12.4, 21.21, 27.3, 32.38, 38.02, 38.02],
            [11, 13.15, 13.15, 15.54, 16.59, 26.61, 33.62, 40.05, 45.8, 45.8],
            [12, 13.9, 13.9, 16.47, 19.16, 28.34, 36.54, 43.1, 49.1, 49.1],
            [13, 14.57, 14.57, 17.36, 20.01, 29.73, 38.55, 44.25, 49.95, 49.95],
            [14, 15.27, 15.27, 18.26, 21.02, 31.33, 40.6, 46.5, 52.4, 52.4],
            [15, 15.82, 15.82, 19.17, 21.99, 32.82, 42.4, 48.1, 53.8, 53.8],
            [16, 16.47, 16.47, 20.32, 23.34, 34.63, 44.65, 50.65, 56.85, 56.85],
            [17, 16.96, 16.96, 21.21, 24.4, 36.21, 46.95, 53.25, 59.75, 59.75],
            [18, 17.28, 17.28, 21.83, 25.46, 37.73, 49.2, 55.9, 62.8, 62.8],
            [19, 17.65, 17.65, 22.32, 26.02, 38.66, 50.25, 57, 63.95, 63.95],
            [20, 18.31, 18.31, 22.65, 26.52, 39.33, 51.9, 59.4, 67.1, 67.1],
            [21, 20.07, 20.07, 24.17, 28.11, 41.99, 54.75, 62.5, 70.55, 70.55],
            [22, 20.64, 20.64, 24.77, 28.98, 42.74, 55.95, 64, 72.15, 72.15],
            [23, 21.2, 21.2, 25.31, 29.63, 43.44, 56.72, 64.89, 73.35, 73.35],
            [24, 21.98, 21.98, 26.3, 31.2, 44.97, 57.85, 66.5, 75.15, 75.15],
            [25, 22.75, 22.75, 27.16, 33.05, 46.34, 58.65, 67.8, 76.45, 76.45],
            [26, 24.01, 24.01, 28.97, 36.29, 48.58, 59.99, 69.6, 78.7, 78.7],
            [27, 25.32, 25.32, 30.19, 38.38, 52.55, 60.75, 71.24, 81.45, 81.45],
            [28, 26.03, 26.03, 30.56, 39.41, 53.81, 61.53, 72.83, 84.5, 84.5],
            [29, 26.77, 26.77, 30.85, 40.42, 54.47, 62.5, 74.43, 86.75, 86.75],
            [30, 27.5, 27.5, 31.27, 41.32, 55.15, 64.15, 76, 88.5, 88.5],
            [31, 28.22, 28.22, 31.56, 41.94, 55.8, 65.03, 77.61, 90.45, 90.45],
            [32, 28.52, 28.52, 32.19, 42.6, 56.4, 65.83, 79.22, 92, 92],
            [33, 28.93, 28.93, 33.02, 43.61, 57.09, 67.04, 80.79, 93.8, 93.8],
            [34, 29.18, 29.18, 33.83, 44.67, 58.23, 68.55, 82.39, 95.55, 95.55],
            [35, 29.48, 29.48, 34.58, 45.28, 59.37, 70.29, 83.98, 96.95, 96.95],
            [36, 29.82, 29.82, 35.53, 45.85, 60.56, 71.97, 85.08, 98.6, 98.6],
            [37, 30.11, 30.11, 36.15, 46.48, 61.56, 73.76, 86.13, 100.2, 100.2],
            [38, 30.39, 30.39, 36.98, 47.04, 62.7, 75.71, 87.08, 101.8, 101.8],
            [39, 30.66, 30.66, 37.8, 47.56, 63.91, 77.42, 89.3, 103.3, 103.3],
            [40, 30.96, 30.96, 38.55, 48.15, 65.15, 78.6, 91.23, 104.7, 104.7],
            [41, 31.27, 31.27, 39.16, 48.64, 65.69, 79.87, 93.11, 106.3, 106.3],
            [42, 31.49, 31.49, 39.44, 49.05, 66.72, 81.21, 94.34, 107.6, 107.6],
            [43, 31.83, 31.83, 39.72, 49.47, 67.75, 83.07, 95.48, 108.7, 108.7],
            [44, 32.04, 32.04, 39.99, 49.88, 68.77, 84.33, 96.58, 110.2, 110.2],
            [45, 32.23, 32.23, 40.26, 50.31, 69.81, 85.23, 97.59, 111.5, 111.5],
            [46, 32.5, 32.5, 40.54, 50.73, 70.84, 86.14, 98.61, 112.75, 112.75],
            [47, 32.72, 32.72, 40.81, 51.14, 71.87, 87, 99.7, 114.05, 114.05],
            [48, 32.98, 32.98, 41.09, 51.56, 72.89, 88.07, 100.63, 115.15, 115.15],
            [49, 33.22, 33.22, 41.35, 51.98, 73.92, 89.23, 101.65, 116.25, 116.25],
            [50, 33.35, 33.35, 41.62, 52.4, 74.96, 90.43, 102.9, 117.45, 117.45],
            [51, 33.81, 33.81, 41.9, 52.79, 76.16, 91.62, 104.33, 118.55, 118.55],
            [52, 34.28, 34.28, 42.18, 53.21, 76.67, 92.47, 105.86, 119.85, 119.85],
            [53, 34.88, 34.88, 42.44, 53.63, 77.26, 93.22, 107.54, 121.35, 121.35],
            [54, 35.36, 35.36, 42.73, 54.04, 77.89, 93.86, 109.04, 123.05, 123.05],
            [55, 35.88, 35.88, 42.99, 54.46, 78.35, 94.61, 110.72, 124.6, 124.6],
            [56, 36.35, 36.35, 43.27, 54.88, 78.91, 95.21, 112.2, 125.85, 125.85],
            [57, 36.89, 36.89, 43.54, 55.3, 79.35, 95.91, 112.9, 126.7, 126.7],
            [58, 37.42, 37.42, 43.81, 55.71, 79.83, 96.46, 113.9, 127.75, 127.75],
            [59, 37.93, 37.93, 44.09, 56.12, 80.29, 96.98, 114.6, 128.65, 128.65],
            [60, 38.38, 38.38, 44.36, 56.53, 80.71, 97.45, 115.3, 129.45, 129.45],
            [61, 38.97, 38.97, 44.62, 56.95, 81.09, 97.97, 116.6, 131.2, 131.2],
            [62, 39.42, 39.42, 44.9, 57.36, 81.43, 98.41, 117.96, 133.3, 133.3],
            [63, 40.1, 40.1, 45.18, 57.79, 81.84, 98.95, 118.51, 135.4, 135.4],
            [64, 40.43, 40.43, 45.44, 58.2, 82.18, 99.38, 119.04, 137.45, 137.45],
            [65, 40.99, 40.99, 45.72, 58.63, 82.42, 99.66, 119.62, 139.45, 139.45],
            [66, 41.5, 41.5, 46, 59.03, 82.77, 100.14, 119.97, 141.6, 141.6],
            [67, 42.09, 42.09, 46.27, 60, 83.05, 100.45, 120.44, 143.35, 143.35],
            [68, 42.56, 42.56, 46.54, 60.73, 83.27, 101.67, 121.05, 144.85, 144.85],
            [69, 43.11, 43.11, 46.82, 61.48, 83.5, 102.85, 121.6, 146.35, 146.35],
            [70, 43.54, 43.54, 47.09, 62.42, 83.75, 104.05, 122.03, 147.95, 147.95],
            [1000, 82.5, 82.5, 104.2, 125.95, 149.9, 171.6, 193.25, 215, 215],
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
