<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Usps;

class ShippingData implements \ArrayAccess
{

    const ORDER = [
        'Service',
        'FirstClassMailType',
        'ZipOrigination',
        'ZipDestination',
        'Pounds',
        'Ounces',
        'Container', //1.RECTANGULAR or NONRECTANGULAR must be
//indicated when <Size>LARGE</Size>.For example: <Container>LEGAL FLAT RATE ENVELOPE</Container>
        'Size', //REGULAR: Package dimensions are 12’’ or less; LARGE: Any package dimension is larger than 12’’
        'Width', //Pieces may not measure more than 108 inches in length and girth combined, except Parcel Select.
        'Length', //Parcel Select parcels may not measure more than 130 inches in length and girth combined.
        'Height',
//'Girth'  //Units are inches. Required when RateV4Request/Size is LARGE,
//and RateV4Request/Container is NONRECTANGULAR or VARIABLE/NULL.
//
        'Machinable' //RateV4Request[Service='FIRST CLASS' and (FirstClassMailType='LETTER'
//or FirstClassMailType='FLAT')] RateV4Request[Service=' Retail Ground’]
//https://pe.usps.com/text/dmm300/201.htm#ep1042477  https://pe.usps.com/text/dmm300/201.htm#a_7_5
//
    ];

    public $data = [];

    public function __construct(array $data)
    {

        foreach (self::ORDER as $key) {
            $this->data[$key] = array_key_exists($key, $data) ? $data[$key] : "";
        }
        //https://github.com/rollun-com/service-usps/issues/1
        //        $url = "http://service-usps.loc/api/datastore/all-price?ZipOrigination=91601-1234&...";
        //        $url = "http://service-usps.loc/api/datastore/all-price?ZipOrigination=91601%2D1234&...";
        if (strpos((string) $this->data['ZipOrigination'], "-") === 5) {
            $this->data['ZipOrigination'] = (int) substr($this->data['ZipOrigination'], 0, 5);
        }
        if (strpos((string) $this->data['ZipDestination'], "-") === 5) {
            $this->data['ZipDestination'] = (int) substr($this->data['ZipDestination'], 0, 5);
        }
        if ($this->data['Pounds'] === "" && $this->data['Ounces'] !== "") {
            $this->data['Pounds'] = 0;
        }
        if ($this->data['Pounds'] !== "" && $this->data['Ounces'] === "") {
            $this->data['Ounces'] = 0;
        }
        $this->data['Machinable'] = (
                !array_key_exists('Machinable', $data) || $data['Machinable'] === "" ) ?
                true : $data['Machinable'];

        $this->data['Size'] = (
                !array_key_exists('Size', $data) || $data['Size'] === "") ?
                'Regular' : $data['Size'];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function toArray()
    {
        return $this->data;
    }
}
