<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps;

use rollun\Entity\Product\Container\FirstClassPackage;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Product\Container\Box;
use rollun\Entity\Product\Container\Envelope;
use rollun\Entity\Product\Container\Tube;
use rollun\Entity\Shipping\Method\ShippingMethodAbstract;
use rollun\Entity\Usps\ShippingData;
use rollun\Entity\Usps\ShippingPriceCommercial;

/**
 * Class ShippingsAbstract
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class ShippingsAbstract extends ShippingMethodAbstract
{
    /**
     * Click_N_Shipp => ['ShortName','Click_N_Shipp','USPS_API_Service',
     * 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width','Length',Weight,'Height',Price]
     */
    const USPS_BOXES = [];

    /**
     * Domestic Zone API URL
     */
    const DOMESTIC_ZONE_API = 'https://postcalc.usps.com/DomesticZoneChart/GetZoneChart?zipCode3Digit=%s&shippingDate=%s';

    /**
     * Domestic Zone file path
     */
    const DOMESTIC_ZONE_FILE = 'data/usps-domestic-zone-%s.json';

    /**
     * @var array
     */
    public $atributes = [];

    /**
     * @var bool
     */
    protected $hasDefinedCost = false;

    /**
     * @inheritDoc
     */
    public function __construct(string $shortName)
    {
        foreach (static::USPS_BOXES as $value) {
            if ($value[0] === $shortName) {
                $this->shortName = $value[0];
                $this->atributes['Click_N_Shipp'] = $value[1];
                $this->atributes['USPS_API_Service'] = $value[2];
                $this->atributes['USPS_API_FirstClassMailType'] = $value[3];
                $this->atributes['USPS_API_Container'] = $value[4];
                $containerClass = $this->getContainerClass($shortName);

                parent::__construct(new $containerClass($value[5], $value[6], $value[7]), $shortName, $value[8]);
            }
        }

        if (empty($this->shortName)) {
            throw new \InvalidArgumentException('No such shipping method');
        }
    }

    /**
     * @return array
     */
    public static function getAllShortNames()
    {
        $result = [];
        foreach (static::USPS_BOXES as $value) {
            $result[] = $value[0];
        }

        return $result;
    }

    /**
     * @param string $shortName
     *
     * @return string
     */
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

    /**
     * @inheritDoc
     */
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
        if (array_key_exists(ShippingPriceCommercial::KEY_RESULT_ERORR, $shippingPriceArray[0])
            && !empty($shippingPriceArray[0][ShippingPriceCommercial::KEY_RESULT_ERORR])) {
            return $shippingPriceArray[0][ShippingPriceCommercial::KEY_RESULT_ERORR];
        }
        if (array_key_exists(ShippingPriceCommercial::KEY_RESULT_PRICE, $shippingPriceArray[0])) {
            return $shippingPriceArray[0][ShippingPriceCommercial::KEY_RESULT_PRICE];
        }

        return null;
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return array|null
     */
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

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return array
     */
    public function getAddData(ShippingRequest $shippingRequest): array
    {
        $sShippingData = $this->getShippingData($shippingRequest);
        $result = array_merge($sShippingData, ['Click_N_Shipp' => $this->atributes['Click_N_Shipp'], 'name' => $this->atributes['Click_N_Shipp']]);
        return $result;
    }

    /**
     * @return bool
     */
    public function hasDefinedCost(): bool
    {
        return $this->hasDefinedCost;
    }

    /**
     * @param bool $hasDefinedCost
     *
     * @return ShippingsAbstract
     */
    public function setDefinedCost(bool $hasDefinedCost): ShippingsAbstract
    {
        $this->hasDefinedCost = $hasDefinedCost;

        return $this;
    }

    /**
     * @param string $zipFrom
     * @param string $zipTo
     *
     * @return int
     * @throws \Exception
     */
    public function getZone(string $zipFrom, string $zipTo): int
    {
        // get zones
        $data = $this->getDomesticZones($zipFrom);

        // prepare destinationZip
        $destinationZip = (int)$this->getTreeDigitsZip($zipTo);

        if (!isset($data['zipCodes'][$destinationZip])) {
            throw new \Exception('No such zip zone');
        }

        return $data['zipCodes'][$destinationZip];
    }

    /**
     * @param string $zipFrom
     *
     * @return array
     * @throws \Exception
     */
    protected function getDomesticZones(string $zipFrom): array
    {
        // prepare 3-digits ZIP Code
        $treeDigitsZip = $this->getTreeDigitsZip($zipFrom);

        // prepare file name
        $fileName = sprintf(self::DOMESTIC_ZONE_FILE, $treeDigitsZip);

        // create file if not exists
        if (!file_exists($fileName)) {
            $data = $this->createDomesticZoneFile($treeDigitsZip, $fileName);
        } else {
            $data = json_decode(file_get_contents($fileName), true);
        }

        return $data;
    }

    /**
     * @param string $treeDigitsZip
     * @param string $fileName
     *
     * @return array
     * @throws \Exception
     */
    protected function createDomesticZoneFile(string $treeDigitsZip, string $fileName): array
    {
        $content = @file_get_contents(sprintf(self::DOMESTIC_ZONE_API, $treeDigitsZip, str_replace('_', '%2F', (new \DateTime())->format('m_d_Y'))));
        if (!empty($content)) {
            // parse content
            $content = json_decode($content, true);

            $i = 0;
            while (isset($content["Column$i"])) {
                foreach ($content["Column$i"] as $row) {
                    $parts = explode('---', $row['ZipCodes']);
                    if (isset($parts[1])) {
                        $from = (int)$parts[0];
                        $to = (int)$parts[1];
                        while ($from <= $to) {
                            $data['zipCodes'][$from] = (int)$row['Zone'];
                            $from++;
                        }
                    } else {
                        $data['zipCodes'][(int)$row['ZipCodes']] = (int)$row['Zone'];
                    }
                }
                $i++;
            }

            // create dir if not exist
            if (!file_exists('data')) {
                mkdir('data', 0777, true);
            }

            if (empty($data)) {
                throw new \Exception('API response parsing failed');
            } else {
                $data['createdAt'] = (new \DateTime())->format('Y-m-d H:i:s');
            }

            // create file
            file_put_contents($fileName, json_encode($data));
        } else {
            throw new \Exception('Domestic zone API unavailable');
        }

        return $data;
    }

    /**
     * @param string $zipCode
     *
     * @return string
     */
    protected function getTreeDigitsZip(string $zipCode): string
    {
        return mb_substr($zipCode, 0, 3);
    }
}
