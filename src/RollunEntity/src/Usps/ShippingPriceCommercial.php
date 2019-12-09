<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Usps;

use USPS\RatePackage as USPSRatePackage;
use rollun\Entity\Usps\Rater;
use rollun\Entity\Usps\ShippingData;

class ShippingPriceCommercial
{

    const KEY_RESULT_ERORR = 'Error';
    const KEY_RESULT_PRICE = 'Price';

    public function __construct(ShippingData $shippingData = null)
    {
    }

    public function getShippingPrice(array $arrayOfShippingData)
    {
        $rate = new Rater(getenv('USPS_API_PASS'));
        // add the package to the rate stack

        foreach ($arrayOfShippingData as $shippingData) {
            $package = $this->getPackage($shippingData);
            $rate->addPackage($package);
        }
        $rate->getRate();

        //return new HtmlResponse(print_r($rate->getHeaders(), true));
        $apiResponse = $rate->getArrayResponse();

        if (array_key_exists('Error', $apiResponse)) {
            $result[self::KEY_RESULT_ERORR] = $apiResponse['Error'] ['Description'];
            return $result;
        }
        if (!(array_key_exists('RateV4Response', $apiResponse) &&
                array_key_exists('Package', $apiResponse['RateV4Response']))
        ) {
            $result[self::KEY_RESULT_ERORR] = "There is not ['RateV4Response']['Package'] key";
            return $result;
        }
        $packagesResults = $apiResponse['RateV4Response']['Package'];
        $packagesResults = array_keys($packagesResults) === range(0, count($packagesResults) - 1) ?
                $packagesResults :
                [$packagesResults];

        $resultArray = [];
        foreach ($packagesResults as $key => $packageResult) {
            $resultArray[$key]['Service'] = $arrayOfShippingData[$key]['Service'];
            $resultArray[$key]['FirstClassMailType'] = $arrayOfShippingData[$key]['FirstClassMailType'] ?? "";
            $resultArray[$key]['Container'] = $arrayOfShippingData[$key]['Container'];

            if (array_key_exists('Postage', $packageResult)) {
                $resultArray[$key][self::KEY_RESULT_PRICE] = $packageResult['Postage'] ['CommercialRate'];
            }
            if (array_key_exists('Error', $packageResult)) {
                $resultArray[$key][self::KEY_RESULT_ERORR] = $packageResult['Error'] ['Description'];
            }
        }
        return $resultArray;
    }

    protected function getPackage($shippingData): USPSRatePackage
    {
        $package = new USPSRatePackage();
        foreach (ShippingData::ORDER as $key) {
            $package->setField($key, $shippingData[$key]);
        }
        return $package;
    }
}
