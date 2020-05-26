<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Shipping\Method\ShippingMethodInterface;
use rollun\Entity\Product\Dimensions\DimensionsInterface;
use rollun\Entity\Product\Container\ContainerInterface as ProductContainerInterface;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\ShippingResponseSet;

abstract class ShippingMethodAbstract implements ShippingMethodInterface
{

    protected $shortName;
    protected $maxWeight;
    protected $name = '';

    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ProductContainerInterface $container, string $shortName, $maxWeight)
    {
        $this->shortName = $shortName;
        $this->maxWeight = $maxWeight;
        $this->container = $container;
    }

    abstract public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false);

    /**
     *
     * @return string 'USPS_FR_Md1' for USPS FlatRate Middle Box 1
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

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
        return $this->passesByWeight($shippingRequest->item) &&
            $this->container->canFit($shippingRequest->item);
    }

    /**
     *
     * @param ShippingRequest $shippingRequest
     * @return ShippingResponseSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89]]
     */
    public function getShippingMetods(ShippingRequest $shippingRequest): ShippingResponseSet
    {
        $cost = $this->getCost($shippingRequest);
        if (is_null($cost) || is_numeric($cost)) {
            $shippingSet[] = [
                ShippingResponseSet::KEY_SHIPPING_METHOD_NAME => $this->shortName,
                ShippingResponseSet::KEY_SHIPPING_METHOD_COST => $cost,
                ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR => null
            ];
        } else {
            $shippingSet[] = [
                ShippingResponseSet::KEY_SHIPPING_METHOD_NAME => $this->shortName,
                ShippingResponseSet::KEY_SHIPPING_METHOD_COST => null,
                ShippingResponseSet::KEY_SHIPPING_METHOD_ERROR => $cost
            ];
        }

        $shippingResponseSet = new ShippingResponseSet($shippingSet);

        $addData = $this->getAddData($shippingRequest);
        $shippingResponseSet->addFildsWithData($addData);

        return $shippingResponseSet;
    }

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
        return [
            'name' => $this->name
        ];
    }
}
