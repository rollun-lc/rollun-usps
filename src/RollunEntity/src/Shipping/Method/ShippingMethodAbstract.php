<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Container\ContainerInterface as ProductContainerInterface;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\ShippingResponseSet;

/**
 * Class ShippingMethodAbstract
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class ShippingMethodAbstract implements ShippingMethodInterface, ShippingMethodProviderInterface
{
    /**
     * Domestic Zone API URL
     */
    const DOMESTIC_ZONE_API = 'https://postcalc.usps.com/DomesticZoneChart/GetZoneChart?zipCode3Digit=%s&shippingDate=%s';

    /**
     * Domestic Zone file path
     */
    const DOMESTIC_ZONE_FILE = 'data/usps-domestic-zone-%s.json';

    /**
     * @var string
     */
    protected $shortName;

    /**
     * @var float
     */
    protected $maxWeight;

    /**
     * @var ProductContainerInterface
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $canShipDangerous = true;

    /**
     * ShippingMethodAbstract constructor.
     *
     * @param ProductContainerInterface $container
     * @param string                    $shortName
     * @param                           $maxWeight
     */
    public function __construct(ProductContainerInterface $container, string $shortName, $maxWeight)
    {
        $this->shortName = $shortName;
        $this->maxWeight = $maxWeight;
        $this->container = $container;
        InsideConstruct::setConstructParams([
            'logger' => LoggerInterface::class,
        ]);
    }

    /**
     *
     * @return string 'USPS_FR_Md1' for USPS FlatRate Middle Box 1
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return ProductContainerInterface
     */
    public function getContainer(): ProductContainerInterface
    {
        return $this->container;
    }

    /**
     * @param ItemInterface $item
     *
     * @return bool
     */
    public function passesByWeight(ItemInterface $item): bool
    {
        $diff = $this->maxWeight - ($item->getWeight() + $this->container->getContainerWeight());
        return abs($diff) < PHP_FLOAT_EPSILON || $diff > 0;
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return bool
     *
     * @todo should return the dimensions of the package after packaging
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return $this->passesByWeight($shippingRequest->item)
            && $this->container->canFit($shippingRequest->item)
            && $this->canShipDangerousMaterials($shippingRequest);
    }

    /**
     *
     * @param ShippingRequest $shippingRequest
     *
     * @return ShippingResponseSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89]]
     */
    public function getShippingMethods(ShippingRequest $shippingRequest): ShippingResponseSet
    {
        // get cost
        $cost = $this->getCost($shippingRequest);

        $row = [
            ShippingResponseSet::KEY_SHIPPING_METHOD_NAME  => $this->shortName,
            ShippingResponseSet::KEY_SHIPPING_METHOD_COST  => $cost,
            ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR => null,
            ShippingResponseSet::KEY_SHIPPING_METHOD_ZONE  => $this->getZone($shippingRequest->getOriginationZipCode(false), $shippingRequest->getDestinationZipCode(false))
        ];

        // @todo fix it. errors should get by another way
        if (!is_null($cost) && !is_numeric($cost)) {
            $row[ShippingResponseSet::KEY_SHIPPING_METHOD_COST] = null;
            $row[ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR] = $cost;
        }

        $shippingResponseSet = new ShippingResponseSet([$row]);

        $addData = $this->getAddData($shippingRequest);
        $shippingResponseSet->addFildsWithData($addData);

        return $shippingResponseSet;
    }

    /**
     * @param ShippingResponseSet $shippingResponseSet
     * @param array               $addData
     *
     * @return ShippingResponseSet
     */
    public function addData($shippingResponseSet, array $addData): ShippingResponseSet
    {
        foreach ($shippingResponseSet as $key => $shippingResponse) {
            $shippingResponseSet[$key] = array_merge($shippingResponse, $addData);
        }

        return $shippingResponseSet;
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return array
     */
    public function getAddData(ShippingRequest $shippingRequest): array
    {
        return [];
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
        $path = sprintf(
            self::DOMESTIC_ZONE_API,
            $treeDigitsZip,
            str_replace('_', '%2F', (new \DateTime())->format('m_d_Y'))
        );
        if (!empty($this->logger)) {
            $this->logger->info("Request to USPS API", [
                'request' => $path,
                'class' => get_class($this),
            ]);
        }
        $content = @file_get_contents($path);
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

    /**
     * @param \DateTime|null $dateTime
     *
     * @return string|null
     */
    protected static function prepareDate(?\DateTime $dateTime): ?string
    {
        return !is_null($dateTime) ? $dateTime->format('d.m.Y') : null;
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return bool
     */
    protected function canShipDangerousMaterials(ShippingRequest $shippingRequest): bool
    {
        // get dangerous attribute
        $dangerous = $shippingRequest->getAttribute('dangerous');

        // is product dangerous ? false by default
        $isDangerous = ($dangerous === 'true' || $dangerous === 1 || $dangerous === true);

        return !(!$this->canShipDangerous && $isDangerous);
    }
}
