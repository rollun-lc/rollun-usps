<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Shipping\Method\ShippingMethodAbstract;
use rollun\Entity\Product\Container\ContainerInterface as ProductContainerInterface;
use rollun\Entity\Shipping\ShippingRequest;

class FixedPrice extends ShippingMethodAbstract
{

    protected $price;

    public function __construct(ProductContainerInterface $container, string $shortName, $maxWeight, $price)
    {
        parent::__construct($container, $shortName, $maxWeight);
        $this->price = $price;
    }

    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        $canBeShipped = $this->canBeShipped($shippingRequest);
        if ($canBeShipped) {
            $price = $this->price;
        } else {
            $price = null;
        }
        return $price;
    }
}
