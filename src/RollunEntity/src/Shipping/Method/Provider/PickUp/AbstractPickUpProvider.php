<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Provider\PickUp;

use rollun\Entity\Shipping\Method\ShippingMethodProvider;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\ShippingResponseSet;


/**
 * Class AbstractPickUpProvider
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class AbstractPickUpProvider extends ShippingMethodProvider
{
    /**
     * @inheritDoc
     */
    public function getShippingMethods(ShippingRequest $shippingRequest): ShippingResponseSet
    {
        if (!in_array((string)$shippingRequest->getOriginationZipCode(), $this->getAllowedOriginationZips())) {
            return new ShippingResponseSet();
        }

        return parent::getShippingMethods($shippingRequest);
    }

    /**
     * @inheritDoc
     */
    public function addCost($shippingResponseSet): ShippingResponseSet
    {
        foreach ($shippingResponseSet as $key => $shippingResponse) {
            $shippingResponseSet[$key]['cost'] = isset($shippingResponse['cost']) ? $shippingResponse['cost'] + $this->getAddCost() : null;
        }

        return $shippingResponseSet;
    }

    /**
     * @return float
     */
    abstract protected function getAddCost(): float;

    /**
     * @return array
     */
    abstract protected function getAllowedOriginationZips(): array;
}
