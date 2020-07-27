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
            [1, 6.92, 6.92, 7.25, 7.46, 7.65, 7.78, 7.90, 8.12, 8.12],
            [2, 7.54, 7.54, 7.74, 8.02, 8.61, 9.79, 10.29, 10.89, 10.89],
            [3, 7.74, 7.74, 8.13, 8.49, 9.34, 11.90, 13.19, 15.39, 15.39],
            [4, 7.84, 7.84, 8.35, 8.97, 10.13, 13.91, 15.76, 17.79, 17.79],
            [5, 7.94, 7.94, 8.40, 9.29, 10.44, 15.89, 18.16, 20.66, 20.66],
            [6, 8.05, 8.05, 8.44, 9.40, 13.98, 18.22, 21.15, 24.17, 24.17],
            [7, 8.29, 8.29, 9.59, 9.64, 15.69, 20.21, 23.88, 27.20, 27.20],
            [8, 8.35, 8.35, 10.07, 11.39, 17.15, 22.22, 26.33, 30.59, 30.59],
            [9, 9.18, 9.18, 10.46, 11.87, 18.40, 24.20, 28.54, 34.05, 34.05],
            [10, 9.66, 9.66, 11.00, 12.06, 20.10, 26.42, 31.73, 37.43, 37.43],
            [11, 11.20, 11.20, 13.43, 14.40, 22.37, 28.90, 35.20, 41.32, 41.32],
            [12, 11.90, 11.90, 14.29, 16.79, 23.98, 31.56, 38.10, 44.38, 44.38],
            [13, 12.52, 12.52, 15.12, 17.58, 25.27, 33.89, 39.65, 45.96, 45.96],
            [14, 13.17, 13.17, 15.96, 18.52, 26.76, 35.81, 41.88, 48.26, 48.26],
            [15, 13.69, 13.69, 16.80, 19.43, 28.14, 37.20, 42.69, 49.53, 49.53],
            [16, 14.29, 14.29, 17.87, 20.68, 29.83, 39.70, 45.54, 52.78, 52.78],
            [17, 14.75, 14.75, 18.70, 21.67, 31.30, 41.73, 47.92, 55.60, 55.60],
            [18, 15.04, 15.04, 19.28, 22.65, 32.71, 43.95, 50.30, 58.40, 58.40],
            [19, 15.39, 15.39, 19.73, 23.17, 33.58, 45.93, 52.65, 61.10, 61.10],
            [20, 16.00, 16.00, 20.04, 23.64, 34.20, 47.13, 54.63, 64.04, 64.04],
            [21, 16.71, 16.71, 20.52, 24.19, 34.81, 47.50, 55.15, 64.87, 64.87],
            [22, 17.24, 17.24, 21.08, 25.00, 35.51, 47.83, 55.58, 65.62, 65.62],
            [23, 17.76, 17.76, 21.58, 25.60, 36.16, 48.09, 55.96, 66.01, 66.01],
            [24, 18.49, 18.49, 22.50, 27.06, 37.59, 49.11, 57.43, 67.63, 67.63],
            [25, 19.20, 19.20, 23.30, 28.78, 38.86, 49.84, 58.87, 68.81, 68.81],
            [26, 20.37, 20.37, 24.99, 31.80, 40.94, 51.06, 60.33, 70.98, 70.98],
            [27, 21.59, 21.59, 26.12, 33.74, 44.64, 51.75, 61.74, 73.65, 73.65],
            [28, 22.25, 22.25, 26.47, 34.70, 45.81, 52.46, 63.18, 76.43, 76.43],
            [29, 22.94, 22.94, 26.74, 35.64, 46.42, 53.34, 64.64, 78.50, 78.50],
            [30, 23.62, 23.62, 27.13, 36.48, 47.06, 54.84, 66.06, 80.19, 80.19],
            [31, 24.29, 24.29, 27.40, 37.05, 47.66, 55.64, 67.53, 81.84, 81.84],
            [32, 24.57, 24.57, 27.98, 37.67, 48.22, 56.37, 68.99, 83.52, 83.52],
            [33, 24.95, 24.95, 28.76, 38.61, 48.86, 57.47, 70.42, 85.06, 85.06],
            [34, 25.18, 25.18, 29.51, 39.59, 49.92, 58.84, 71.87, 86.66, 86.66],
            [35, 25.46, 25.46, 30.21, 40.16, 50.98, 60.42, 73.32, 88.16, 88.16],
            [36, 25.78, 25.78, 31.09, 40.69, 52.09, 61.95, 74.32, 89.66, 89.66],
            [37, 26.05, 26.05, 31.67, 41.28, 53.02, 63.58, 75.27, 91.14, 91.14],
            [38, 26.31, 26.31, 32.44, 41.80, 54.08, 65.35, 76.14, 92.59, 92.59],
            [39, 26.56, 26.56, 33.20, 42.28, 55.20, 66.90, 78.15, 94.03, 94.03],
            [40, 26.84, 26.84, 33.90, 42.83, 56.36, 67.98, 79.91, 95.32, 95.32],
            [41, 27.13, 27.13, 34.47, 43.29, 56.86, 69.13, 81.62, 96.70, 96.70],
            [42, 27.33, 27.33, 34.73, 43.67, 57.82, 70.35, 82.74, 98.03, 98.03],
            [43, 27.65, 27.65, 34.99, 44.06, 58.78, 72.04, 83.77, 99.29, 99.29],
            [44, 27.84, 27.84, 35.24, 44.44, 59.73, 73.19, 84.77, 100.41, 100.41],
            [45, 28.02, 28.02, 35.49, 44.84, 60.69, 74.00, 85.69, 101.69, 101.69],
            [46, 28.27, 28.27, 35.75, 45.23, 61.65, 74.83, 86.62, 102.92, 102.92],
            [47, 28.48, 28.48, 36.00, 45.61, 62.61, 75.61, 87.61, 104.06, 104.06],
            [48, 28.72, 28.72, 36.26, 46.00, 63.56, 76.59, 88.45, 105.17, 105.17],
            [49, 28.94, 28.94, 36.50, 46.39, 64.52, 77.64, 89.38, 106.24, 106.24],
            [50, 29.06, 29.06, 36.76, 46.78, 65.48, 78.73, 90.52, 107.36, 107.36],
            [51, 29.49, 29.49, 37.02, 47.15, 66.60, 79.81, 91.82, 108.36, 108.36],
            [52, 29.93, 29.93, 37.28, 47.54, 67.07, 80.59, 93.21, 109.65, 109.65],
            [53, 30.49, 30.49, 37.52, 47.93, 67.62, 81.27, 94.74, 111.05, 111.05],
            [54, 30.93, 30.93, 37.79, 48.31, 68.21, 81.85, 96.10, 112.61, 112.61],
            [55, 31.42, 31.42, 38.03, 48.70, 68.64, 82.53, 97.63, 114.13, 114.13],
            [56, 31.85, 31.85, 38.29, 49.09, 69.16, 83.08, 99.01, 115.30, 115.30],
            [57, 32.36, 32.36, 38.54, 49.48, 69.57, 83.71, 100.52, 116.32, 116.32],
            [58, 32.85, 32.85, 38.79, 49.86, 70.01, 84.21, 101.85, 117.29, 117.29],
            [59, 33.32, 33.32, 39.05, 50.24, 70.44, 84.69, 102.55, 118.15, 118.15],
            [60, 33.74, 33.74, 39.30, 50.63, 70.83, 85.11, 103.15, 119.00, 119.00],
            [61, 34.29, 34.29, 39.55, 51.02, 71.19, 85.59, 103.75, 120.60, 120.60],
            [62, 34.71, 34.71, 39.81, 51.40, 71.50, 85.99, 104.21, 122.53, 122.53],
            [63, 35.34, 35.34, 40.07, 51.80, 71.88, 86.48, 104.71, 124.50, 124.50],
            [64, 35.65, 35.65, 40.31, 52.18, 72.20, 86.87, 105.19, 126.41, 126.41],
            [65, 36.17, 36.17, 40.57, 52.58, 72.42, 87.12, 105.72, 128.39, 128.39],
            [66, 36.64, 36.64, 40.83, 52.95, 72.75, 87.56, 106.04, 130.26, 130.26],
            [67, 37.19, 37.19, 41.08, 53.85, 73.01, 87.84, 106.46, 132.00, 132.00],
            [68, 37.63, 37.63, 41.33, 54.53, 73.21, 88.95, 107.02, 133.40, 133.40],
            [69, 38.14, 38.14, 41.59, 55.23, 73.43, 90.02, 107.52, 134.81, 134.81],
            [70, 38.54, 38.54, 41.84, 56.10, 73.66, 91.11, 107.91, 136.27, 136.27],
            [1000, 76.95, 76.95, 97.85, 118.75, 139.65, 160.55, 181.45, 202.35, 202.35],
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
