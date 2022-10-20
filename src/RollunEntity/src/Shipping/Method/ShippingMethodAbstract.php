<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

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
     * @var bool
     */
    protected $canShipDangerous = true;

    /**
     * @var DomesticZoneService
     */
    private $domesticZoneService;

    /**
     * ShippingMethodAbstract constructor.
     *
     * @param ProductContainerInterface $container
     * @param string $shortName
     * @param                           $maxWeight
     * @param DomesticZoneService|null $domesticZoneService
     */
    public function __construct(
        ProductContainerInterface $container,
        string $shortName,
        $maxWeight,
        DomesticZoneService $domesticZoneService = null
    ){
        $this->shortName = $shortName;
        $this->maxWeight = $maxWeight;
        $this->container = $container;
        $this->domesticZoneService = $domesticZoneService ?? $this->createDomesticZoneService();
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
        // У ShippingMethodAbstract есть много наследников, и родительский конструктор в некоторых не вызывается,
        // поэтому domesticZoneService не всегда проинициализирован.
        // (Раньше функционал domesticZoneService был в этом классе, но понадобилось его вынести в отдельный)
        // Было решено, что проще всего добавить тут проверку, и инициализировать его если поле пустое
        if (empty($this->domesticZoneService)) {
            $this->domesticZoneService = $this->createDomesticZoneService();
        }
        return $this->domesticZoneService->getZone($zipFrom, $zipTo);
    }

    private function createDomesticZoneService(): DomesticZoneService
    {
        return new DomesticZoneService(self::DOMESTIC_ZONE_API, self::DOMESTIC_ZONE_FILE);
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
