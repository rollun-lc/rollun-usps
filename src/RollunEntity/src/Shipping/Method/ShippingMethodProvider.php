<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\ShippingResponseSet;

/**
 * Class ShippingMethodProvider
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class ShippingMethodProvider implements ShippingMethodProviderInterface, \IteratorAggregate
{
    /**
     *
     * @var array shippingMethods
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $shortName;

    /**
     * ShippingMethodProvider constructor.
     *
     * @param string $shortName
     * @param array  $data
     */
    public function __construct($shortName, array $data = [])
    {
        $this->shortName = $shortName;
        foreach ($data as $shippingMethod) {
            /* @var $shippingMethod ShippingMethodInterface */
            $this->data[$shippingMethod->getShortName()] = $shippingMethod;
        }
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return ShippingResponseSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89],[['id'  =>...]]
     */
    public function getShippingMethods(ShippingRequest $shippingRequest): ShippingResponseSet
    {
        $shippingResponseSet = new ShippingResponseSet();

        foreach ($this->data as $shippingMethod) {
            /* @var $shippingMethod ShippingMethodInterface */
            $childShippingResponseSet = $shippingMethod->getShippingMethods($shippingRequest);
            $childShippingResponseSet = $this->addCost($childShippingResponseSet);
            $shippingResponseSet->mergeResponseSet($childShippingResponseSet, $this->getShortName());
        }
        $addData = $this->getAddData($shippingRequest);
        $shippingResponseSet->addFildsWithData($addData);

        return $shippingResponseSet;
    }

    /**
     * @param ShippingResponseSet $shippingResponseSet
     *
     * @return ShippingResponseSet
     */
    public function addCost($shippingResponseSet): ShippingResponseSet
    {
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
     *
     * @return string 'USPS_FR_Md1' for USPS FlatRate Middle Box 1
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }
}
