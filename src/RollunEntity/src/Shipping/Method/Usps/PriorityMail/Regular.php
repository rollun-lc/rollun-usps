<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Product\Container\ContainerInterface as ProductContainerInterface;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract as UspsShippingsAbstract;
use rollun\Entity\Product\Item\Product;

class Regular extends UspsShippingsAbstract
{

    /**
     * Click_N_Shipp => ['id','Click_N_Shipp','USPS_API_Service',
     * 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width','Length','Height',Weight]
     */
    const USPS_BOXES = [
        ['PM-Regular', 'Priority Mail',
            'PRIORITY COMMERCIAL', '', 'VARIABLE', 12, 12, 12, 70],
        ['PM-Large', 'Priority Mail',
            'PRIORITY COMMERCIAL', '', 'RECTANGULAR', 108, 0, 0, 70],
    ];

    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        $item = $shippingRequest->item;
        if (!($item instanceof Product )) {
            return false;
        }
        if ($this->shortName === 'PM-Large') {
            $dimensions = $item->getDimensionsList()[0]['dimensions'];
            $maxDimension = $dimensions->getDimensionsRecord()['Length'];
            if ($maxDimension <= 12) {
                return false;
            }
        }
        return parent::canBeShipped($shippingRequest);
    }

    public function getShippingData(ShippingRequest $shippingRequest)
    {
        if ($this->shortName === 'PM-Regular') {
            return parent::getShippingData($shippingRequest);
        }

        $shippingData = array_merge(parent::getShippingData($shippingRequest), ['Size' => 'LARGE']);
        return $shippingData;
    }
}
