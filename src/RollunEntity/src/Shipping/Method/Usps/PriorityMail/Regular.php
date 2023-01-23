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
            [1, 7.64, 7.78, 8.01, 8.24, 8.47, 8.96, 9.43, 10.07, 18.03],
            [2, 8.23, 8.31, 8.45, 8.74, 9.39, 11.29, 11.97, 13.17, 27.59],
            [3, 8.42, 8.58, 8.86, 9.25, 10.23, 13.74, 15.48, 18.45, 37.43],
            [4, 8.55, 8.76, 9.16, 9.83, 12.01, 16.01, 18.57, 21.6, 45.07],
            [5, 8.74, 8.95, 9.3, 10.27, 13.42, 17.54, 21.43, 24.97, 52.45],
            [6, 8.91, 9.13, 9.73, 10.78, 14.35, 19.09, 24.49, 28.53, 60.1],
            [7, 9.16, 9.39, 10.36, 11.41, 15.34, 20.62, 26.57, 31.01, 67.49],
            [8, 9.68, 9.92, 10.97, 12.11, 16.39, 22.18, 28.64, 33.5, 75.76],
            [9, 10.26, 10.51, 11.64, 12.89, 17.49, 23.72, 30.72, 35.97, 84.25],
            [10, 10.82, 11.09, 12.31, 13.65, 18.57, 25.26, 32.8, 38.24, 91.62],
            [11, 11.49, 11.78, 12.96, 14.41, 19.65, 26.8, 34.81, 40.49, 100.1],
            [12, 11.99, 12.29, 13.61, 15.16, 20.8, 28.34, 36.7, 42.74, 107.31],
            [13, 12.53, 12.84, 14.26, 15.9, 21.93, 29.86, 38.58, 45, 111.14],
            [14, 13.11, 13.44, 14.9, 16.64, 23.05, 31.27, 40.47, 47.26, 116.64],
            [15, 13.53, 13.87, 15.54, 17.37, 24.17, 32.66, 42.36, 49.52, 119.74],
            [16, 14.22, 14.57, 16.18, 18.1, 25.29, 34.07, 44.25, 51.77, 126.31],
            [17, 14.72, 15.09, 16.81, 18.83, 26.39, 35.46, 46.14, 54.03, 132.97],
            [18, 15.31, 15.69, 17.44, 19.55, 27.5, 36.86, 48.02, 56.28, 139.67],
            [19, 15.82, 16.22, 18.07, 20.28, 28.62, 38.27, 49.91, 58.53, 146.3],
            [20, 16.27, 16.68, 18.7, 21, 29.72, 39.67, 51.8, 60.8, 153.04],
            [21, 16.75, 17.17, 19.36, 22.13, 30.97, 41.8, 53.7, 62.72, 156.3],
            [22, 17.75, 18.2, 20.73, 23.88, 32.68, 44.04, 55.68, 64.7, 158.13],
            [23, 18.81, 19.28, 22.21, 25.78, 34.48, 46.41, 57.72, 66.74, 159.06],
            [24, 19.95, 20.45, 23.79, 27.85, 36.4, 48.9, 59.84, 68.84, 162.94],
            [25, 21.16, 21.69, 25.5, 30.1, 38.42, 51.53, 62.05, 71.01, 165.75],
            [26, 23.66, 26.16, 30.34, 39.1, 50.9, 64.83, 76.85, 89.34, 174.05],
            [27, 25.15, 27.62, 31.73, 41.52, 55.54, 65.72, 78.81, 92.66, 180.63],
            [28, 26.02, 28.32, 32.17, 42.71, 57.01, 66.64, 80.69, 96.37, 187.41],
            [29, 26.9, 29.01, 32.51, 43.89, 57.78, 67.78, 82.6, 99.09, 192.42],
            [30, 27.79, 29.73, 32.98, 44.92, 58.58, 69.73, 84.47, 101.21, 196.58],
            [31, 28.67, 30.41, 33.31, 45.64, 59.34, 70.76, 86.38, 103.57, 202.22],
            [32, 28.96, 30.86, 34.03, 46.41, 60.04, 71.71, 88.29, 105.46, 206.34],
            [33, 29.36, 31.47, 34.99, 47.58, 60.84, 73.13, 90.17, 107.64, 210.15],
            [34, 29.56, 31.94, 35.92, 48.8, 62.17, 74.92, 92.07, 109.76, 214.13],
            [35, 29.84, 32.44, 36.77, 49.5, 63.51, 76.96, 93.96, 111.46, 217.77],
            [36, 30.13, 33.03, 37.86, 50.17, 64.9, 78.95, 95.26, 113.45, 221.48],
            [37, 30.41, 33.47, 38.57, 50.89, 66.06, 81.06, 96.52, 115.4, 225.14],
            [38, 30.63, 33.97, 39.52, 51.54, 67.4, 83.36, 97.64, 117.33, 228.74],
            [39, 30.87, 34.47, 40.47, 52.14, 68.81, 85.37, 100.29, 119.15, 232.28],
            [40, 31.13, 34.95, 41.32, 52.83, 70.26, 86.77, 102.58, 120.86, 235.42],
            [41, 31.45, 35.41, 42.02, 53.4, 70.88, 88.25, 104.82, 122.79, 240.71],
            [42, 31.69, 35.68, 42.35, 53.87, 72.09, 89.84, 106.29, 124.36, 244],
            [43, 32.09, 36.05, 42.66, 54.35, 73.29, 92.03, 107.64, 125.7, 247.12],
            [44, 32.31, 36.31, 42.97, 54.83, 74.49, 93.52, 108.95, 127.51, 249.97],
            [45, 32.52, 36.56, 43.29, 55.32, 75.69, 94.57, 110.15, 129.1, 253.13],
            [46, 32.82, 36.87, 43.61, 55.81, 76.9, 95.66, 111.36, 130.61, 256.13],
            [47, 33.06, 37.13, 43.92, 56.28, 78.1, 96.67, 112.66, 132.18, 259],
            [48, 33.35, 37.43, 44.24, 56.77, 79.29, 97.92, 113.76, 133.52, 261.82],
            [49, 33.62, 37.72, 44.54, 57.26, 80.5, 99.29, 114.98, 134.85, 264.38],
            [50, 33.75, 37.91, 44.85, 57.75, 81.72, 100.7, 116.47, 136.3, 267.21],
            [51, 34.3, 38.37, 45.17, 58.2, 83.12, 102.11, 118.17, 137.64, 271.85],
            [52, 34.86, 38.84, 45.48, 58.69, 83.71, 103.11, 119.98, 139.2, 275.03],
            [53, 35.59, 39.42, 45.79, 59.17, 84.4, 104, 121.99, 141.03, 278.54],
            [54, 36.17, 39.9, 46.11, 59.65, 85.13, 104.75, 123.76, 143.08, 282.44],
            [55, 36.79, 40.4, 46.41, 60.13, 85.68, 105.64, 125.77, 144.97, 286.25],
            [56, 37.35, 40.87, 46.74, 60.62, 86.33, 106.34, 127.53, 146.48, 289.21],
            [57, 38.01, 41.4, 47.05, 61.1, 86.85, 107.17, 128.37, 147.52, 291.79],
            [58, 38.65, 41.91, 47.36, 61.57, 87.41, 107.81, 129.56, 148.79, 294.16],
            [59, 39.26, 42.42, 47.68, 62.05, 87.93, 108.43, 130.39, 149.87, 296.35],
            [60, 39.79, 42.86, 47.99, 62.52, 88.44, 108.98, 131.22, 150.84, 298.44],
            [61, 40.51, 43.42, 48.29, 63.01, 88.87, 109.6, 132.77, 152.97, 302.49],
            [62, 41.06, 43.89, 48.61, 63.48, 89.27, 110.12, 134.38, 155.52, 307.25],
            [63, 41.88, 44.52, 48.93, 63.97, 89.74, 110.76, 135.04, 158.06, 312.19],
            [64, 42.27, 44.88, 49.23, 64.46, 90.15, 111.26, 135.66, 160.54, 316.99],
            [65, 42.94, 45.41, 49.54, 64.96, 90.42, 111.59, 136.36, 162.97, 321.94],
            [66, 43.56, 45.93, 49.87, 65.43, 90.84, 112.16, 136.77, 165.58, 326.63],
            [67, 44.28, 46.49, 50.18, 66.55, 91.17, 112.52, 137.33, 167.7, 330.93],
            [68, 44.83, 46.95, 50.48, 67.38, 91.41, 113.96, 138.06, 169.51, 334.43],
            [69, 45.5, 47.49, 50.81, 68.26, 91.69, 115.35, 138.71, 171.34, 338],
            [70, 46.01, 47.92, 51.12, 69.34, 91.99, 116.76, 139.23, 173.27, 341.63],
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

            return parent::canShipDangerousMaterials($shippingRequest);
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
