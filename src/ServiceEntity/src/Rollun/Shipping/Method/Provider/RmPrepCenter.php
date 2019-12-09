<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace service\Entity\Rollun\Shipping\Method\Provider;

use rollun\Entity\Shipping\Method\ShippingMethodProvider;
use rollun\ShippingMethod\ShippingMethodInterface;
use rollun\Entity\Shipping\ShippingResponseSet;

class RmPrepCenter extends ShippingMethodProvider
{

    const ADD_COST = 1;

    public function addCost($shippingResponseSet): ShippingResponseSet
    {

        foreach ($shippingResponseSet as $key => $shippingResponse) {
            $shippingResponseSet[$key]['cost'] = isset($shippingResponse['cost']) ?
                    $shippingResponse['cost'] + static::ADD_COST : null;
        }
        return $shippingResponseSet;
    }
}
