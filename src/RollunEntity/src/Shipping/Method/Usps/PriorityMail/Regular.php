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
            [1, 7.02, 7.02, 7.35, 7.56, 7.80, 7.98, 8.15, 8.42, 11.40],
            [2, 7.64, 7.64, 7.84, 8.12, 8.76, 9.99, 10.54, 11.19, 17.45],
            [3, 7.84, 7.84, 8.23, 8.59, 9.54, 12.15, 13.49, 15.74, 23.67],
            [4, 7.94, 7.94, 8.45, 9.07, 10.33, 14.16, 16.06, 18.14, 28.50],
            [5, 8.04, 8.04, 8.50, 9.39, 10.64, 16.14, 18.46, 21.01, 33.17],
            [6, 8.15, 8.15, 8.54, 9.50, 14.18, 18.47, 21.45, 24.52, 38.01],
            [7, 8.39, 8.39, 9.69, 9.74, 15.89, 20.46, 24.18, 27.55, 42.68],
            [8, 8.45, 8.45, 10.17, 11.49, 17.35, 22.47, 26.63, 30.94, 47.91],
            [9, 9.28, 9.28, 10.56, 11.97, 18.6, 24.45, 28.84, 34.4, 53.28],
            [10, 9.76, 9.76, 11.1, 12.16, 20.3, 26.67, 32.03, 37.78, 57.94],
            [11, 11.3, 11.3, 13.53, 14.5, 22.57, 29.15, 35.5, 41.67, 63.3],
            [12, 12, 12, 14.39, 16.89, 24.18, 31.81, 38.4, 44.73, 67.86],
            [13, 12.62, 12.62, 15.22, 17.68, 25.47, 34.14, 39.95, 46.31, 70.29],
            [14, 13.27, 13.27, 16.06, 18.62, 26.96, 36.06, 42.18, 48.61, 73.77],
            [15, 13.79, 13.79, 16.9, 19.53, 28.34, 37.45, 42.99, 49.88, 75.72],
            [16, 14.39, 14.39, 17.97, 20.78, 30.03, 39.95, 45.84, 53.13, 79.88],
            [17, 14.85, 14.85, 18.8, 21.77, 31.5, 41.98, 48.22, 55.95, 84.09],
            [18, 15.14, 15.14, 19.38, 22.75, 32.91, 44.2, 50.6, 58.75, 88.33],
            [19, 15.49, 15.49, 19.83, 23.27, 33.78, 46.18, 52.95, 61.54, 92.52],
            [20, 16.1, 16.1, 20.14, 23.74, 34.4, 47.38, 54.93, 64.39, 96.78],
            [21, 16.81, 16.81, 20.62, 24.29, 35.01, 47.75, 55.45, 65.22, 98.85],
            [22, 17.34, 17.34, 21.18, 25.1, 35.71, 48.08, 55.88, 65.97, 100],
            [23, 17.86, 17.86, 21.68, 25.7, 36.36, 48.34, 56.26, 66.36, 100.59],
            [24, 18.59, 18.59, 22.6, 27.16, 37.79, 49.36, 57.73, 67.98, 103.05],
            [25, 19.3, 19.3, 23.4, 28.88, 39.06, 50.09, 59.17, 69.16, 104.82],
            [26, 20.47, 20.47, 25.09, 31.9, 41.14, 51.31, 60.63, 71.33, 108.1],
            [27, 21.69, 21.69, 26.22, 33.84, 44.84, 52, 62.04, 74, 112.19],
            [28, 22.35, 22.35, 26.57, 34.8, 46.01, 52.71, 63.48, 76.78, 116.4],
            [29, 23.04, 23.04, 26.84, 35.74, 46.62, 53.59, 64.94, 78.85, 119.51],
            [30, 23.72, 23.72, 27.23, 36.58, 47.26, 55.09, 66.36, 80.54, 122.1],
            [31, 24.39, 24.39, 27.5, 37.15, 47.86, 55.89, 67.83, 82.19, 125.6],
            [32, 24.67, 24.67, 28.08, 37.77, 48.42, 56.62, 69.29, 83.87, 128.16],
            [33, 25.05, 25.05, 28.86, 38.71, 49.06, 57.72, 70.72, 85.41, 130.53],
            [34, 25.28, 25.28, 29.61, 39.69, 50.12, 59.09, 72.17, 87.01, 133],
            [35, 25.56, 25.56, 30.31, 40.26, 51.18, 60.67, 73.62, 88.51, 135.26],
            [36, 25.88, 25.88, 31.19, 40.79, 52.29, 62.2, 74.62, 90.01, 137.57],
            [37, 26.15, 26.15, 31.77, 41.38, 53.22, 63.83, 75.57, 91.49, 139.84],
            [38, 26.41, 26.41, 32.54, 41.9, 54.28, 65.6, 76.44, 92.94, 142.07],
            [39, 26.66, 26.66, 33.3, 42.38, 55.4, 67.15, 78.45, 94.38, 144.27],
            [40, 26.94, 26.94, 34, 42.93, 56.56, 68.23, 80.21, 95.67, 146.22],
            [41, 27.23, 27.23, 34.57, 43.39, 57.06, 69.38, 81.92, 97.05, 149.51],
            [42, 27.43, 27.43, 34.83, 43.77, 58.02, 70.60, 83.04, 98.38, 151.55],
            [43, 27.75, 27.75, 35.09, 44.16, 58.98, 72.29, 84.07, 99.64, 153.49],
            [44, 27.94, 27.94, 35.34, 44.54, 59.93, 73.44, 85.07, 100.76, 155.26],
            [45, 28.12, 28.12, 35.59, 44.94, 60.89, 74.25, 85.99, 102.04, 157.22],
            [46, 28.37, 28.37, 35.85, 45.33, 61.85, 75.08, 86.92, 103.27, 159.09],
            [47, 28.58, 28.58, 36.10, 45.71, 62.81, 75.86, 87.91, 104.41, 160.87],
            [48, 28.82, 28.82, 36.36, 46.10, 63.76, 76.84, 88.75, 105.52, 162.62],
            [49, 29.04, 29.04, 36.60, 46.49, 64.72, 77.89, 89.68, 106.59, 164.21],
            [50, 29.16, 29.16, 36.86, 46.88, 65.68, 78.98, 90.82, 107.71, 165.97],
            [51, 29.59, 29.59, 37.12, 47.25, 66.80, 80.06, 92.12, 108.71, 168.85],
            [52, 30.03, 30.03, 37.38, 47.64, 67.27, 80.84, 93.51, 110.00, 170.82],
            [53, 30.59, 30.59, 37.62, 48.03, 67.82, 81.52, 95.04, 111.40, 173.01],
            [54, 31.03, 31.03, 37.89, 48.41, 68.41, 82.10, 96.40, 112.96, 175.43],
            [55, 31.52, 31.52, 38.13, 48.80, 68.84, 82.78, 97.93, 114.48, 177.79],
            [56, 31.95, 31.95, 38.39, 49.19, 69.36, 83.33, 99.31, 115.65, 179.63],
            [57, 32.46, 32.46, 38.64, 49.58, 69.77, 83.96, 100.82, 116.67, 181.24],
            [58, 32.95, 32.95, 38.89, 49.96, 70.21, 84.46, 102.15, 117.64, 182.70],
            [59, 33.42, 33.42, 39.15, 50.34, 70.64, 84.94, 102.85, 118.50, 184.07],
            [60, 33.84, 33.84, 39.40, 50.73, 71.03, 85.36, 103.45, 119.35, 185.36],
            [61, 34.39, 34.39, 39.65, 51.12, 71.39, 85.84, 104.05, 120.95, 187.88],
            [62, 34.81, 34.81, 39.91, 51.50, 71.70, 86.24, 104.51, 122.88, 190.84],
            [63, 35.44, 35.44, 40.17, 51.90, 72.08, 86.73, 105.01, 124.85, 193.90],
            [64, 35.75, 35.75, 40.41, 52.28, 72.40, 87.12, 105.49, 126.76, 196.89],
            [65, 36.27, 36.27, 40.67, 52.68, 72.62, 87.37, 106.02, 128.74, 199.97],
            [66, 36.74, 36.74, 40.93, 53.05, 72.95, 87.81, 106.34, 130.61, 202.87],
            [67, 37.29, 37.29, 41.18, 53.95, 73.21, 88.09, 106.76, 132.35, 205.54],
            [68, 37.73, 37.73, 41.43, 54.63, 73.41, 89.20, 107.32, 133.75, 207.72],
            [69, 38.24, 38.24, 41.69, 55.33, 73.63, 90.27, 107.82, 135.16, 209.94],
            [70, 38.64, 38.64, 41.94, 56.20, 73.86, 91.36, 108.21, 136.62, 212.19],
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
