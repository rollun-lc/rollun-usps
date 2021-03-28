<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class Regular
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Regular extends ShippingsAbstract
{
    /**
     * @var bool
     */
    protected $canShipDangerous = false;

    /**
     * Click_N_Shipp => ['id', 'Click_N_Shipp', 'USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width', 'Length', 'Height', 'Weight']
     *
     * 1) The weight limit for Priority Mail items is 70 lbs.
     * 2) The maximum size for Priority Mail items is 108 inches in combined length and girth (the length of the longest side, plus the distance around its thickest part).
     * 3) Length, Width, Height are required for accurate pricing of a rectangular package when any dimension of the item exceeds 12 inches. In addition, Girth is required for non-rectangular packages.
     */
    const USPS_BOXES
        = [
            ['PM-Regular', 'Priority Mail', 'PRIORITY COMMERCIAL', '', 'VARIABLE', 12, 12, 12, 70],
            ['PM-Large', 'Priority Mail', 'PRIORITY COMMERCIAL', '', 'RECTANGULAR', 108, 108, 108, 70],
        ];

    /**
     * Regular costs, got from https://pe.usps.com/text/dmm300/Notice123.htm#_c078
     */
    const USPS_BOXES_COSTS
        = [
            [1, 7.16, 7.16, 7.46, 7.67, 7.88, 8.34, 8.52, 8.80, 14.25],
            [2, 7.79, 7.79, 7.96, 8.24, 8.85, 10.44, 11.01, 11.69, 21.81],
            [3, 8.00, 8.00, 8.35, 8.72, 9.64, 12.70, 14.10, 16.45, 29.59],
            [4, 8.10, 8.10, 8.58, 9.21, 10.43, 14.80, 16.78, 18.96, 35.63],
            [5, 8.20, 8.20, 8.63, 9.53, 11.70, 16.87, 19.29, 21.96, 41.46],
            [6, 8.31, 8.31, 8.67, 9.64, 14.32, 19.39, 22.52, 25.75, 47.51],
            [7, 8.73, 8.73, 10.08, 10.13, 16.53, 21.48, 25.39, 28.93, 53.35],
            [8, 8.79, 8.79, 10.58, 11.95, 18.04, 23.59, 27.96, 32.49, 59.89],
            [9, 9.65, 9.65, 10.98, 12.45, 19.34, 25.67, 30.28, 36.12, 66.60],
            [10, 10.15, 10.15, 11.54, 12.65, 21.11, 28.00, 33.63, 39.67, 72.43],
            [11, 12.15, 12.15, 14.54, 15.59, 24.26, 32.07, 39.05, 45.84, 79.13],
            [12, 12.90, 12.90, 15.47, 18.16, 25.99, 34.99, 42.24, 49.20, 84.83],
            [13, 13.57, 13.57, 16.36, 19.01, 27.38, 37.55, 43.95, 50.94, 87.86],
            [14, 14.27, 14.27, 17.26, 20.02, 28.98, 39.67, 46.40, 53.47, 92.21],
            [15, 14.82, 14.82, 18.17, 20.99, 30.47, 41.20, 47.29, 54.87, 94.65],
            [16, 15.47, 15.47, 19.32, 22.34, 32.28, 43.95, 50.42, 58.44, 99.85],
            [17, 15.96, 15.96, 20.21, 23.40, 33.86, 46.18, 53.04, 61.55, 105.11],
            [18, 16.28, 16.28, 20.83, 24.46, 35.38, 48.62, 55.66, 64.63, 110.41],
            [19, 16.65, 16.65, 21.32, 25.02, 36.31, 50.80, 58.25, 67.69, 115.65],
            [20, 17.31, 17.31, 21.65, 25.52, 36.98, 52.12, 60.42, 70.83, 120.98],
            [21, 18.07, 18.07, 22.17, 26.11, 37.64, 52.53, 61.00, 71.74, 123.56],
            [22, 18.64, 18.64, 22.77, 26.98, 38.39, 52.89, 61.47, 72.57, 125.00],
            [23, 19.20, 19.20, 23.31, 27.63, 39.09, 53.17, 61.89, 73.00, 125.74],
            [24, 19.98, 19.98, 24.30, 29.20, 40.62, 54.30, 63.50, 74.78, 128.81],
            [25, 20.75, 20.75, 25.16, 31.05, 41.99, 55.10, 65.09, 76.08, 131.03],
            [26, 22.01, 22.01, 26.97, 34.29, 44.23, 56.44, 66.69, 78.46, 135.13],
            [27, 23.32, 23.32, 28.19, 36.38, 48.20, 57.20, 68.24, 81.40, 140.24],
            [28, 24.03, 24.03, 28.56, 37.41, 49.46, 57.98, 69.83, 84.46, 145.50],
            [29, 24.77, 24.77, 28.85, 38.42, 50.12, 58.95, 71.43, 86.74, 149.39],
            [30, 25.50, 25.50, 29.27, 39.32, 50.80, 60.60, 73.00, 88.59, 152.63],
            [31, 26.22, 26.22, 29.56, 39.94, 51.45, 61.48, 74.61, 90.41, 157.00],
            [32, 26.52, 26.52, 30.19, 40.60, 52.05, 62.28, 76.22, 92.26, 160.20],
            [33, 26.93, 26.93, 31.02, 41.61, 52.74, 63.49, 77.79, 93.95, 163.16],
            [34, 27.18, 27.18, 31.83, 42.67, 53.88, 65.00, 79.39, 95.71, 166.25],
            [35, 27.48, 27.48, 32.58, 43.28, 55.02, 66.74, 80.98, 97.36, 169.08],
            [36, 27.82, 27.82, 33.53, 43.85, 56.21, 68.42, 82.08, 99.01, 171.96],
            [37, 28.11, 28.11, 34.15, 44.48, 57.21, 70.21, 83.13, 100.64, 174.80],
            [38, 28.39, 28.39, 34.98, 45.04, 58.35, 72.16, 84.08, 102.23, 177.59],
            [39, 28.66, 28.66, 35.80, 45.56, 59.56, 73.87, 86.30, 103.82, 180.34],
            [40, 28.96, 28.96, 36.55, 46.15, 60.80, 75.05, 88.23, 105.24, 182.78],
            [41, 29.27, 29.27, 37.16, 46.64, 61.34, 76.32, 90.11, 106.76, 186.89],
            [42, 29.49, 29.49, 37.44, 47.05, 62.37, 77.66, 91.34, 108.22, 189.44],
            [43, 29.83, 29.83, 37.72, 47.47, 63.40, 79.52, 92.48, 109.60, 191.86],
            [44, 30.04, 30.04, 37.99, 47.88, 64.42, 80.78, 93.58, 110.84, 194.08],
            [45, 30.23, 30.23, 38.26, 48.31, 65.46, 81.68, 94.59, 112.24, 196.53],
            [46, 30.50, 30.50, 38.54, 48.73, 66.49, 82.59, 95.61, 113.60, 198.86],
            [47, 30.72, 30.72, 38.81, 49.14, 67.52, 83.45, 96.70, 114.85, 201.09],
            [48, 30.98, 30.98, 39.09, 49.56, 68.54, 84.52, 97.63, 116.07, 203.28],
            [49, 31.22, 31.22, 39.35, 49.98, 69.57, 85.68, 98.65, 117.25, 205.26],
            [50, 31.35, 31.35, 39.62, 50.40, 70.61, 86.88, 99.90, 118.48, 207.46],
            [51, 31.81, 31.81, 39.90, 50.79, 71.81, 88.07, 101.33, 119.58, 211.06],
            [52, 32.28, 32.28, 40.18, 51.21, 72.32, 88.92, 102.86, 121.00, 213.53],
            [53, 32.88, 32.88, 40.44, 51.63, 72.91, 89.67, 104.54, 122.54, 216.26],
            [54, 33.36, 33.36, 40.73, 52.04, 73.54, 90.31, 106.04, 124.26, 219.29],
            [55, 33.88, 33.88, 40.99, 52.46, 74.00, 91.06, 107.72, 125.93, 222.24],
            [56, 34.35, 34.35, 41.27, 52.88, 74.56, 91.66, 109.24, 127.22, 224.54],
            [57, 34.89, 34.89, 41.54, 53.30, 75.00, 92.36, 110.90, 128.34, 226.55],
            [58, 35.42, 35.42, 41.81, 53.71, 75.48, 92.91, 112.37, 129.40, 228.38],
            [59, 35.93, 35.93, 42.09, 54.12, 75.94, 93.43, 113.14, 130.35, 230.09],
            [60, 36.38, 36.38, 42.36, 54.53, 76.36, 93.90, 113.80, 131.29, 231.70],
            [61, 36.97, 36.97, 42.62, 54.95, 76.74, 94.42, 114.46, 133.05, 234.85],
            [62, 37.42, 37.42, 42.90, 55.36, 77.08, 94.86, 114.96, 135.17, 238.55],
            [63, 38.10, 38.10, 43.18, 55.79, 77.49, 95.40, 115.51, 137.34, 242.38],
            [64, 38.43, 38.43, 43.44, 56.20, 77.83, 95.83, 116.04, 139.44, 246.11],
            [65, 38.99, 38.99, 43.72, 56.63, 78.07, 96.11, 116.62, 141.61, 249.96],
            [66, 39.50, 39.50, 44.00, 57.03, 78.42, 96.59, 116.97, 143.67, 253.59],
            [67, 40.09, 40.09, 44.27, 58.00, 78.70, 96.90, 117.44, 145.59, 256.93],
            [68, 40.56, 40.56, 44.54, 58.73, 78.92, 98.12, 118.05, 147.13, 259.65],
            [69, 41.11, 41.11, 44.82, 59.48, 79.15, 99.30, 118.60, 148.68, 262.43],
            [70, 41.54, 41.54, 45.09, 60.42, 79.40, 100.50, 119.03, 150.28, 265.24],
        ];

    /**
     * @var bool
     */
    protected $hasDefinedCost = true;

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        $item = $shippingRequest->item;
        if (!($item instanceof Product)) {
            return false;
        }

        if ($this->shortName === 'PM-Large') {
            // The weight limit for Priority Mail items is 70 lbs.
            if ($this->getWeight($shippingRequest) > 70) {
                return false;
            }

            /** @var array $dimensions */
            $dimensions = $item->getDimensionsList()[0]['dimensions']->getDimensionsRecord();

            // exit because it PM-Regular shipping
            if ($dimensions['Length'] <= 12) {
                return false;
            }

            // The maximum size for Priority Mail items is 108 inches in combined length and girth
            if (($dimensions['Girth'] + $dimensions['Length']) > 108) {
                return false;
            }

            return true;
        }

        return parent::canBeShipped($shippingRequest);
    }

    /**
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        if ($this->canBeShipped($shippingRequest)) {
            // prepare weight
            $weight = $this->getWeight($shippingRequest);

            foreach (self::USPS_BOXES_COSTS as $row) {
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
        $weight = $shippingRequest->item->getWeight();
        if ($this->shortName === 'PM-Large') {
            $lbs = $shippingRequest->item->getVolume() / 166;
            if ($lbs > $weight) {
                $weight = $lbs;
            }
        }

        return $weight;
    }
}
