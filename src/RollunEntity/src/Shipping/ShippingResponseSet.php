<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping;

/**
 * Class ShippingResponseSet
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class ShippingResponseSet extends \ArrayObject
{
    const KEY_SHIPPING_METHOD_NAME = 'id';
    const KEY_SHIPPING_METHOD_COST = 'cost';
    const KEY_SHIPPING_METHOD_TRACK_NUMBER_DATE = 'trackNumberDate';
    const KEY_SHIPPING_METHOD_SEND_DATE = 'shippingSendDate';
    const KEY_SHIPPING_METHOD_ARRIVE_DATE = 'shippingArriveDate';
    const KEY_SHIPPING_METHOD_ERROR = 'Error';

    const SHIPPING_METHOD_NAME_SEPARATOR = '-';

    /**
     * @param array $shippingSet [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89], ['id'  => 'RMATV-DS','cost' =>8.95]]
     */
    public function __construct($shippingSet = [])
    {
        parent::__construct($shippingSet);
    }

    /**
     * @param \rollun\Entity\Shipping\ShippingResponseSet $responseSet
     * @param string                                      $prefixName
     *
     * @return array [['id'  => 'RMATV-USPS-FRLG1','cost' =>17.89], ['id'  => 'RMATV-DS','cost' =>8.95]]
     */
    public function mergeResponseSet(ShippingResponseSet $responseSet, string $prefixName)
    {
        foreach ($responseSet as $shippingRecord) {
            $shippingRecord[self::KEY_SHIPPING_METHOD_NAME] = $prefixName
                . self::SHIPPING_METHOD_NAME_SEPARATOR
                . $shippingRecord[self::KEY_SHIPPING_METHOD_NAME];
            $this->append($shippingRecord);
        }
        return $this->getArrayCopy();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * @return mixed|null
     */
    public function getBestCostResponseRec()
    {
        $keyBest = null;
        $bestShippingCost = null;
        foreach ($this as $key => $shippingRecord) {
            if (!is_null($shippingRecord['cost'])
                && (is_null($bestShippingCost) || $shippingRecord['cost'] < $bestShippingCost)) {
                $bestShippingCost = $shippingRecord['cost'];
                $keyBest = $key;
            }
        }

        return is_null($keyBest) ? null : $this[$keyBest];
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function addFildsWithData(array $data)
    {
        foreach ($this as $key => $shippingRecord) {
            $this[$key] = array_merge($shippingRecord, $data);
        }

        return $this;
    }
}
