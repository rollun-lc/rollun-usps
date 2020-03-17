<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps;

use rollun\Entity\Shipping\Method\Usps\PriorityMail\FlatRate;
use rollun\Entity\Shipping\Method\Usps\FirstClass\Package;
use rollun\Entity\Shipping\Method\Usps\PriorityMail\RegionalRate;
use rollun\Entity\Shipping\Method\Usps\PriorityMail\Regular;
use rollun\Entity\Shipping\Method\ShippingMethodProvider;
use rollun\Entity\Usps\ShippingData;
use rollun\Entity\Shipping\ShippingResponseSet;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Usps\ShippingPriceCommercial;

class UspsProvider extends ShippingMethodProvider
{

    const SHORT_NAME = 'Usps';

    public function __construct()
    {

        $shippingMethods = [];

        $classes = [FlatRate::class, Package::class, RegionalRate::class, Regular::class];
        foreach ($classes as $oneClass) {
            $shortNames = $oneClass::getAllShortNames();
            foreach ($shortNames as $shortName) {
                $shippingMethods[] = new $oneClass($shortName);
            }
        }

        parent::__construct(static::SHORT_NAME, $shippingMethods);
    }

    /**
     *
     * @param ShippingRequest $shippingRequest
     * @return ShippingResponseSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89],[['id'  =>...]]
     */
    public function getShippingMetods(ShippingRequest $shippingRequest): ShippingResponseSet
    {
        $shippingResponseSet = new ShippingResponseSet();
        $requestedShippingMetods = [];
        $shippingDataArray = [];
        foreach ($this->data as $shippingMethod) {
            if ($shippingMethod->hasDefinedCost()) {
                $calculatedhippingResponseSet = $shippingMethod->getShippingMetods($shippingRequest);
                $calculatedhippingResponseSet = $this->addCost($calculatedhippingResponseSet);
                $shippingResponseSet->mergeResponseSet($calculatedhippingResponseSet, $this->getShortName());
            } elseif ($shippingMethod->canBeShipped($shippingRequest)) {
                $requestedShippingMetods[] = $shippingMethod;
                $shippingDataArray[] = new ShippingData($shippingMethod->getShippingData($shippingRequest));
            } else {
                $shippingSet = [
                    [
                        ShippingResponseSet::KEY_SHIPPING_METHOD_NAME => $shippingMethod->getShortName(),
                        ShippingResponseSet::KEY_SHIPPING_METHOD_COST => null,
                        ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR => 'Can not be shipped',
                    ]
                ];
                $canNotBeShippedResponseSet = new ShippingResponseSet($shippingSet);
                $addData = $shippingMethod->getAddData($shippingRequest);
                $canNotBeShippedResponseSet->addFildsWithData($addData);
                $shippingResponseSet->mergeResponseSet($canNotBeShippedResponseSet, $this->getShortName());
            }
        }

        if (!empty($shippingDataArray)) {
            $shippingPriceCommercial = new ShippingPriceCommercial();
            $shippingPriceArray = $shippingPriceCommercial->getShippingPrice($shippingDataArray);
            foreach ($shippingPriceArray as $key => $responseRec) {
                if (is_string($responseRec)) {
                    throw new \RuntimeException(sprintf('Received error message from usps: %s.', $responseRec));
                }
                $shippingSet = [];
                if (array_key_exists(ShippingPriceCommercial::KEY_RESULT_ERORR, $responseRec) &&
                    !empty($responseRec[ShippingPriceCommercial::KEY_RESULT_ERORR])) {
                    $shippingSet[] = [
                        ShippingResponseSet::KEY_SHIPPING_METHOD_NAME
                        => $requestedShippingMetods[$key]->getShortName(),
                        ShippingResponseSet::KEY_SHIPPING_METHOD_COST
                        => null,
                        ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR
                        => $responseRec[ShippingPriceCommercial::KEY_RESULT_ERORR],
                    ];
                } else {
                    $shippingSet[] = [
                        ShippingResponseSet::KEY_SHIPPING_METHOD_NAME
                        => $requestedShippingMetods[$key]->getShortName(),
                        ShippingResponseSet::KEY_SHIPPING_METHOD_COST
                        => $responseRec[ShippingPriceCommercial::KEY_RESULT_PRICE],
                        ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR
                        => null,
                    ];
                }
                $requestedResponseSet = new ShippingResponseSet($shippingSet);
                $addData = $requestedShippingMetods[$key]->getAddData($shippingRequest);
                $requestedResponseSet->addFildsWithData($addData);
                $shippingResponseSet->mergeResponseSet($requestedResponseSet, $this->getShortName());
            }
        }
        return $shippingResponseSet;
    }
}
