<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Shipping\Method\ShippingMethodInterface;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\ShippingResponseSet;

class ShippingMethodProvider implements ShippingMethodInterface, \IteratorAggregate
{

    /**
     *
     * @var array shippingMethods
     */
    protected $data = [];
    protected $shortName;

    public function __construct($shortName, array $data = [])
    {
        $this->shortName = $shortName;
        foreach ($data as $shippingMethod) {
            /* @var $shippingMethod ShippingMethodInterface */
            $this->data[$shippingMethod->getShortName()] = $shippingMethod;
        }
    }

    /**
     *
     * @param ShippingRequest $shippingRequest
     * @return ShippingResponseSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89],[['id'  =>...]]
     */
    public function getShippingMetods(ShippingRequest $shippingRequest): ShippingResponseSet
    {
        $shippingResponseSet = new ShippingResponseSet();

        foreach ($this->data as $shippingMethod) {
            /* @var $shippingMethod ShippingMethodInterface */
            $childShippingResponseSet = $shippingMethod->getShippingMetods($shippingRequest);
            $childShippingResponseSet = $this->addCost($childShippingResponseSet);
            $shippingResponseSet->mergeResponseSet($childShippingResponseSet, $this->getShortName());
        }
        $addData = $this->getAddData($shippingRequest);
        $shippingResponseSet->addFildsWithData($addData);
        return $shippingResponseSet;
    }

    public function addCost($shippingResponseSet): ShippingResponseSet
    {
//        foreach ($shippingResponseSet as $key => $shippingResponse) {
//            $shippingResponseSet[$key]['cost'] = $shippingResponse['cost'] + 0;
//        }
        return $shippingResponseSet;
    }

    public function getAddData(ShippingRequest $shippingRequest): array
    {
        return [];
    }

    /**
     *
     * @return string 'USPS_FR_Md1' for USPS FlatRate Middle Box 1
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }
}
