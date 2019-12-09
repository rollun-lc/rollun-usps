<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping;

use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Subject\Address;

class ShippingRequest
{

    const RESPONSE_KEYS = [
    ];

    /**
     *
     * @var ItemInterface
     */
    public $item;

    /**
     *
     * @var Address
     */
    public $addressOrigination;

    /**
     *
     * @var Address
     */
    public $addressDestination;

    /**
     *
     * @var array
     */
    protected $attributes = [];

    public function __construct(
        ItemInterface $item,
        Address $addressOrigination = null,
        Address $addressDestination = null,
        array $attributes = []
    ) {
        $this->item = $item;
        $this->addressOrigination = $addressOrigination;
        $this->addressDestination = $addressDestination;
        $this->attributes = $attributes;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function setAttributes($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getOriginationZipCode($full = true)
    {
        return $full ? $this->addressOrigination->getZipCode() : $this->addressOrigination->zip5;
    }

    public function getDestinationZipCode($full = true)
    {
        return $full ? $this->addressDestination->getZipCode() : $this->addressDestination->zip5;
    }

    public function getDataForResonse()
    {

        return $data;
    }
}
