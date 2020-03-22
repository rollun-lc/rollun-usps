<?php
declare(strict_types=1);

namespace rollun\test\unit\Entity\Shipping\Method;

use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Subject\Address;

/**
 * Class CreateShippingRequestTrait
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
trait CreateShippingRequestTrait
{
    /**
     * @param float  $width
     * @param float  $length
     * @param float  $height
     * @param float  $weight
     * @param string $zipFrom
     * @param string $zipTo
     *
     * @return ShippingRequest
     */
    protected function createShippingRequest(float $width, float $length, float $height, float $weight, string $zipFrom, string $zipTo): ShippingRequest
    {
        $addressOrigination = new Address('', $zipFrom);
        $addressDestination = new Address('', $zipTo);

        $rectangular = new Rectangular($length, $width, $height);
        $product = new Product($rectangular, $weight);

        return new ShippingRequest($product, $addressOrigination, $addressDestination);
    }
}
