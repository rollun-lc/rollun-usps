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
            /* oz, zone 1, zone 2, zone 3, zone 4, zone 5, zone 6, zone 7, zone 8, zone 9*/
            [1, 7.01, 7.01, 7.31, 7.52, 7.63, 7.84, 7.97, 8.15, 8.15],
            [2, 7.59, 7.59, 7.76, 8.04, 8.55, 9.79, 10.26, 10.79, 10.79],
            [3, 7.75, 7.75, 8.10, 8.47, 9.29, 11.80, 13.00, 15.05, 15.05],
            [4, 7.85, 7.85, 8.33, 8.96, 10.08, 13.90, 15.68, 17.56, 17.56],
            [5, 7.95, 7.95, 8.38, 9.28, 11.35, 15.97, 18.19, 20.56, 20.56],
            [6, 8.06, 8.06, 8.42, 9.39, 13.97, 18.49, 21.42, 24.35, 24.35],
            [7, 8.33, 8.33, 9.68, 9.73, 16.03, 20.33, 23.84, 27.08, 27.08],
            [8, 8.39, 8.39, 10.18, 11.55, 17.54, 22.44, 26.41, 30.64, 30.64],
            [9, 9.25, 9.25, 10.58, 12.05, 18.84, 24.52, 28.73, 34.27, 34.27],
            [10, 9.65, 9.65, 11.04, 12.15, 20.46, 26.55, 31.63, 37.27, 37.27],
            [11, 11.65, 11.65, 14.04, 15.09, 23.61, 30.62, 37.05, 42.80, 42.80],
            [12, 12.40, 12.40, 14.97, 17.66, 25.34, 33.54, 40.10, 46.10, 46.10],
            [13, 13.07, 13.07, 15.86, 18.51, 26.73, 35.55, 41.25, 46.95, 46.95],
            [14, 13.77, 13.77, 16.76, 19.52, 28.33, 37.60, 43.50, 49.40, 49.40],
            [15, 14.32, 14.32, 17.67, 20.49, 29.82, 39.40, 45.10, 50.80, 50.80],
            [16, 14.97, 14.97, 18.82, 21.84, 31.63, 41.65, 47.65, 53.85, 53.85],
            [17, 15.46, 15.46, 19.71, 22.90, 33.21, 43.95, 50.25, 56.75, 56.75],
            [18, 15.78, 15.78, 20.33, 23.96, 34.73, 46.20, 52.90, 59.80, 59.80],
            [19, 16.15, 16.15, 20.82, 24.52, 35.66, 47.25, 54.00, 60.95, 60.95],
            [20, 16.81, 16.81, 21.15, 25.02, 36.33, 48.90, 56.40, 64.10, 64.10],
            [21, 17.57, 17.57, 21.67, 25.61, 36.99, 49.75, 57.50, 65.55, 65.55],
            [22, 18.14, 18.14, 22.27, 26.48, 37.74, 50.95, 59.00, 67.15, 67.15],
            [23, 18.70, 18.70, 22.81, 27.13, 38.44, 51.72, 59.89, 68.35, 68.35],
            [24, 19.48, 19.48, 23.80, 28.70, 39.97, 52.85, 61.50, 70.15, 70.15],
            [25, 20.25, 20.25, 24.66, 30.55, 41.34, 53.65, 62.80, 71.45, 71.45],
            [26, 21.51, 21.51, 26.47, 33.79, 43.58, 54.99, 64.60, 73.70, 73.70],
            [27, 22.82, 22.82, 27.69, 35.88, 47.55, 55.75, 66.24, 76.45, 76.45],
            [28, 23.53, 23.53, 28.06, 36.91, 48.81, 56.53, 67.83, 79.50, 79.50],
            [29, 24.27, 24.27, 28.35, 37.92, 49.47, 57.50, 69.43, 81.75, 81.75],
            [30, 25.00, 25.00, 28.77, 38.82, 50.15, 59.15, 71.00, 83.50, 83.50],
            [31, 25.72, 25.72, 29.06, 39.44, 50.80, 60.03, 72.61, 85.45, 85.45],
            [32, 26.02, 26.02, 29.69, 40.10, 51.40, 60.83, 74.22, 87.00, 87.00],
            [33, 26.43, 26.43, 30.52, 41.11, 52.09, 62.04, 75.79, 88.80, 88.80],
            [34, 26.68, 26.68, 31.33, 42.17, 53.23, 63.55, 77.39, 90.55, 90.55],
            [35, 26.98, 26.98, 32.08, 42.78, 54.37, 65.29, 78.98, 91.95, 91.95],
            [36, 27.32, 27.32, 33.03, 43.35, 55.56, 66.97, 80.08, 93.60, 93.60],
            [37, 27.61, 27.61, 33.65, 43.98, 56.56, 68.76, 81.13, 95.20, 95.20],
            [38, 27.89, 27.89, 34.48, 44.54, 57.70, 70.71, 82.08, 96.80, 96.80],
            [39, 28.16, 28.16, 35.30, 45.06, 58.91, 72.42, 84.30, 98.30, 98.30],
            [40, 28.46, 28.46, 36.05, 45.65, 60.15, 73.60, 86.23, 99.70, 99.70],
            [41, 28.77, 28.77, 36.66, 46.14, 60.69, 74.87, 88.11, 101.30, 101.30],
            [42, 28.99, 28.99, 36.94, 46.55, 61.72, 76.21, 89.34, 102.60, 102.60],
            [43, 29.33, 29.33, 37.22, 46.97, 62.75, 78.07, 90.48, 103.70, 103.70],
            [44, 29.54, 29.54, 37.49, 47.38, 63.77, 79.33, 91.58, 105.20, 105.20],
            [45, 29.73, 29.73, 37.76, 47.81, 64.81, 80.23, 92.59, 106.50, 106.50],
            [46, 30.00, 30.00, 38.04, 48.23, 65.84, 81.14, 93.61, 107.75, 107.75],
            [47, 30.22, 30.22, 38.31, 48.64, 66.87, 82.00, 94.70, 109.05, 109.05],
            [48, 30.48, 30.48, 38.59, 49.06, 67.89, 83.07, 95.63, 110.15, 110.15],
            [49, 30.72, 30.72, 38.85, 49.48, 68.92, 84.23, 96.65, 111.25, 111.25],
            [50, 30.85, 30.85, 39.12, 49.90, 69.96, 85.43, 97.90, 112.45, 112.45],
            [51, 31.31, 31.31, 39.40, 50.29, 71.16, 86.62, 99.33, 113.55, 113.55],
            [52, 31.78, 31.78, 39.68, 50.71, 71.67, 87.47, 100.86, 114.85, 114.85],
            [53, 32.38, 32.38, 39.94, 51.13, 72.26, 88.22, 102.54, 116.35, 116.35],
            [54, 32.86, 32.86, 40.23, 51.54, 72.89, 88.86, 104.04, 118.05, 118.05],
            [55, 33.38, 33.38, 40.49, 51.96, 73.35, 89.61, 105.72, 119.60, 119.60],
            [56, 33.85, 33.85, 40.77, 52.38, 73.91, 90.21, 107.20, 120.85, 120.85],
            [57, 34.39, 34.39, 41.04, 52.80, 74.35, 90.91, 107.90, 121.70, 121.70],
            [58, 34.92, 34.92, 41.31, 53.21, 74.83, 91.46, 108.90, 122.75, 122.75],
            [59, 35.43, 35.43, 41.59, 53.62, 75.29, 91.98, 109.60, 123.65, 123.65],
            [60, 35.88, 35.88, 41.86, 54.03, 75.71, 92.45, 110.30, 124.45, 124.45],
            [61, 36.47, 36.47, 42.12, 54.45, 76.09, 92.97, 111.60, 126.20, 126.20],
            [62, 36.92, 36.92, 42.40, 54.86, 76.43, 93.41, 112.96, 128.30, 128.30],
            [63, 37.60, 37.60, 42.68, 55.29, 76.84, 93.95, 113.51, 130.40, 130.40],
            [64, 37.93, 37.93, 42.94, 55.70, 77.18, 94.38, 114.04, 132.45, 132.45],
            [65, 38.49, 38.49, 43.22, 56.13, 77.42, 94.66, 114.62, 134.45, 134.45],
            [66, 39.00, 39.00, 43.50, 56.53, 77.77, 95.14, 114.97, 136.60, 136.60],
            [67, 39.59, 39.59, 43.77, 57.50, 78.05, 95.45, 115.44, 138.35, 138.35],
            [68, 40.06, 40.06, 44.04, 58.23, 78.27, 96.67, 116.05, 139.85, 139.85],
            [69, 40.61, 40.61, 44.32, 58.98, 78.50, 97.85, 116.60, 141.35, 141.35],
            [70, 41.04, 41.04, 44.59, 59.92, 78.75, 99.05, 117.03, 142.95, 142.95],
            [1000, 80.00, 80.00, 101.70, 123.45, 144.90, 166.60, 188.25, 210.00, 210.00],
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
}
