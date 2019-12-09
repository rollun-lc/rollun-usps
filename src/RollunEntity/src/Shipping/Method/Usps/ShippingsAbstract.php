<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps;

use rollun\Entity\Product\Container\ContainerInterface as ProductContainerInterface;
use rollun\Entity\Product\Container\FirstClassPackage;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\Method\FixedPrice;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Product\Container\Envelope;
use rollun\Entity\Product\Container\Tube;
use rollun\Entity\Shipping\Method\ShippingMethodAbstract;
use rollun\Entity\Usps\ShippingData;
use rollun\Entity\Usps\ShippingDataManager;
use rollun\Entity\Usps\ShippingPriceCommercial;

abstract class ShippingsAbstract extends ShippingMethodAbstract
{

    /**
     * Click_N_Shipp => ['ShortName','Click_N_Shipp','USPS_API_Service',
     * 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width','Length',Weight,'Height',Price]
     */
    const USPS_BOXES = [
    ];

    public $atributes = [];

    public function __construct(string $shortName)
    {
        //TODO: need refactor
        foreach (static::USPS_BOXES as $value) {
            if ($value[0] === $shortName) {
                $this->shortName = $value[0];
                $this->atributes['Click_N_Shipp'] = $value[1];
                $this->atributes['USPS_API_Service'] = $value[2];
                $this->atributes['USPS_API_FirstClassMailType'] = $value[3];
                $this->atributes['USPS_API_Container'] = $value[4];
                $length = $value[5];
                $weight = $value[6];
                $height = $value[7];
                $containerClass = $this->getContainerClass($shortName);
                $container = new $containerClass($length, $weight, $height);
                $maxWeight = $value[8];
                $price = isset($value[9]) ? $value[9] : null;
                if (property_exists(static::class, 'price')) {
                    $this->price = $price;
                }
            }
        }
        parent::__construct($container, $shortName, $maxWeight, $price);
    }

    public static function getAllShortNames()
    {
        $result = [];
        foreach (static::USPS_BOXES as $value) {
            $result[] = $value[0];
        }
        return $result;
    }

    protected function getContainerClass($shortName)
    {
        if (false !== strpos($shortName, 'Env')) {
            return Envelope::class;
        }
        if (false !== strpos($shortName, 'Large')) {
            return Tube::class;
        }
        if (false !== strpos($shortName, 'FtCls')) {
            return FirstClassPackage::class;
        }
        return Box::class;
    }

    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        $canBeShipped = $this->canBeShipped($shippingRequest);
        if (!$canBeShipped) {
            return null;
        }
        $shippingData = $this->getShippingData($shippingRequest);

        $shippingDataObject = new ShippingData($shippingData);

        if ($shippingDataOnly) {
            return $shippingDataObject;
        }

        $shippingPriceCommercial = new ShippingPriceCommercial();

        $shippingPriceArray = $shippingPriceCommercial->getShippingPrice([$shippingDataObject]);
        if (array_key_exists(ShippingPriceCommercial::KEY_RESULT_ERORR, $shippingPriceArray[0]) &&
            !empty($shippingPriceArray[0][ShippingPriceCommercial::KEY_RESULT_ERORR])) {
            return $shippingPriceArray[0][ShippingPriceCommercial::KEY_RESULT_ERORR];
        }
        if (array_key_exists(ShippingPriceCommercial::KEY_RESULT_PRICE, $shippingPriceArray[0])) {
            return $shippingPriceArray[0][ShippingPriceCommercial::KEY_RESULT_PRICE];
        }

        return null;
    }

    public function getShippingData(ShippingRequest $shippingRequest)
    {
        $shippingData['Service'] = $this->atributes['USPS_API_Service'];
        $shippingData['FirstClassMailType'] = $this->atributes['USPS_API_FirstClassMailType'];
        $shippingData['Container'] = $this->atributes['USPS_API_Container'];

        $shippingData['ZipOrigination'] = $shippingRequest->getOriginationZipCode(false);
        $shippingData['ZipDestination'] = $shippingRequest->getDestinationZipCode(false);


        $shippingData['Pounds'] = $shippingRequest->item->getWeight();
        //$shippingData['Ounces'] = '';

        $dimensionsList = $shippingRequest->item->getDimensionsList();
        if (count($dimensionsList) !== 1) {
            return null;
        }
        if ($dimensionsList[0]['quantity'] > 1) {
            return null;
        }
        //it is just single item
        $dimensions = $dimensionsList[0]['dimensions'];
        /* @var $dimensions DimensionsInterface */

        $shippingData = array_merge($shippingData, $dimensions->getDimensionsRecord());


        return $shippingData;
        //'Ounces',     =0
        //'Size', //REGULAR: Package dimensions are 12’’ or less; LARGE: Any package dimension is larger than 12’’
    }

    public function getAddData(ShippingRequest $shippingRequest): array
    {
        $sShippingData = $this->getShippingData($shippingRequest);
        $result = array_merge($sShippingData, ['Click_N_Shipp' => $this->atributes['Click_N_Shipp']]);
        return $result;
    }
}
