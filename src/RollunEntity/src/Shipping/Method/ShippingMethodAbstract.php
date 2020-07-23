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
     * @var string
     */
    protected $shortName;

    /**
     * @var float
     */
    protected $maxWeight;

    /**
     *
     * @var ProductContainerInterface
     */
    protected $container;

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
        return $this->passesByWeight($shippingRequest->item) && $this->container->canFit($shippingRequest->item);
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
            ShippingResponseSet::KEY_SHIPPING_METHOD_NAME         => $this->shortName,
            ShippingResponseSet::KEY_SHIPPING_METHOD_COST         => $cost,
            ShippingResponseSet::KEY_SHIPPING_METHOD_TRACK_NUMBER => $this->getTrackNumber($shippingRequest),
            ShippingResponseSet::KEY_SHIPPING_METHOD_SEND_DATE    => self::prepareDate($this->getShippingSendDate($shippingRequest)),
            ShippingResponseSet::KEY_SHIPPING_METHOD_ARRIVE_DATE  => self::prepareDate($this->getShippingArriveDate($shippingRequest)),
            ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR        => null,
        ];

        // @todo fix it. errors should get by another way
        if (!is_null($cost) && !is_numeric($cost)) {
            $row[ShippingResponseSet::KEY_SHIPPING_METHOD_COST] = $cost;
            $row[ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR] = null;
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
     * @param ShippingRequest $shippingRequest
     *
     * @return string|null
     */
    public function getTrackNumber(ShippingRequest $shippingRequest): ?string
    {
        return null;
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return \DateTime|null
     */
    public function getShippingSendDate(ShippingRequest $shippingRequest): ?\DateTime
    {
        return null;
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return \DateTime|null
     */
    public function getShippingArriveDate(ShippingRequest $shippingRequest): ?\DateTime
    {
        return null;
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
}
