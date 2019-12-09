<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Usps;

class ShippingDataManager
{

    const SERVICE = [
        'FIRST CLASS COMMERCIAL',
        'PRIORITY COMMERCIAL'
            //
            //'PARCEL' https://github.com/VinceG/USPS-php-api/blob/master/src/RatePackage.php
            //or
            //Retail Ground https://www.usps.com/business/web-tools-apis/rate-calculator-api.pdf
            //
            //Priority Mail Cubic
    ];
    const FIRST_CLASS_MAIL_TYPE = [
//        'POSTCARD',
//        'LETTER',
//        'FLAT',
//        'PACKAGE SERVICE RETAIL',
        'PACKAGE SERVICE']; //https://pe.usps.com/BusinessMail101/Index?ViewName=PackageServices
    const CONTEINERS = [
//      'SM FLAT RATE ENVELOPE',
//        'WINDOW FLAT RATE ENVELOPE',
//        'GIFT CARD FLAT RATE ENVELOPE',
        'FLAT RATE ENVELOPE',
        'LEGAL FLAT RATE ENVELOPE',
        'PADDED FLAT RATE ENVELOPE',
        'SM FLAT RATE BOX',
        'MD FLAT RATE BOX',
        'LG FLAT RATE BOX',
        'REGIONAL RATE BOX A',
        'REGIONAL RATE BOX B',
        'VARIABLE',
    ];

    //Parcel Select Ground

    /**
     *
     * @param ShippingData $shippingData
     */
    protected $shippingData;

    public function __construct(ShippingData $shippingData)
    {
        $this->shippingData = $shippingData;
    }

    public function getArrayOfShippingData()
    {
        switch ($this->shippingData['Service']) {
            case 'FIRST CLASS COMMERCIAL':
                if ($this->shippingData['FirstClassMailType'] <> "") {
                    return [$this->shippingData];
                } else {
                    return $this->getFirstClassCommercial();
                }

            case 'PRIORITY COMMERCIAL':
                if ($this->shippingData['Container'] <> "") {
                    return [$this->shippingData];
                } else {
                    return $this->getPriorityMailCommercial();
                }

            case '':
                return $this->getAllServices();

            default:
                throw (
                new \InvalidArgumentException('"Service" must be "" or "FirstClassMailType" or "PRIORITY COMMERCIAL"')
                );
        }
    }

    public function getFirstClassCommercial()
    {
        foreach (self::FIRST_CLASS_MAIL_TYPE as $type) {
            $shippingData = clone $this->shippingData;
            $shippingData['Service'] = 'FIRST CLASS COMMERCIAL';
            $shippingData['FirstClassMailType'] = $type;
            $shippingData['Container'] = "";
            $result[] = $shippingData;
        }
        return $result;
    }

    public function getPriorityMailCommercial()
    {
        foreach (self::CONTEINERS as $conteiner) {
            $shippingData = clone $this->shippingData;
            $shippingData['Service'] = 'PRIORITY COMMERCIAL';
            $shippingData['FirstClassMailType'] = "";
            $shippingData['Container'] = $conteiner;
            $shippingData['Size'] = 'Regular';
            $result[] = $shippingData;
        }
        return $result;
    }

    public function getAllServices()
    {
        return
                array_merge($this->getFirstClassCommercial(), $this->getPriorityMailCommercial());
    }
}
