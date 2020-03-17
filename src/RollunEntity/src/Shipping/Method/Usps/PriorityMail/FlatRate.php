<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class FlatRate
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class FlatRate extends ShippingsAbstract
{
    /**
     * Click_N_Shipp => ['ShortName','Click_N_Shipp','USPS_API_Service',
     * 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width','Length',Weight,'Height',Price]
     */
    const USPS_BOXES
        = [
            /*['PM-FR-Env', 'Priority Mail Flat Rate Envelope','PRIORITY COMMERCIAL', '', 'FLAT RATE ENVELOPE', 12, 9.5, 0, 70, 6.95],*/
            ['PM-FR-LegalEnv', 'Priority Mail Legal Flat Rate Envelope', 'PRIORITY COMMERCIAL', '', 'LEGAL FLAT RATE ENVELOPE', 15, 9.5, 0, 70, 7.25],
            ['PM-FR-Pad-Env', 'Priority Mail Flat Rate Padded Envelope', 'PRIORITY COMMERCIAL', '', 'PADDED FLAT RATE ENVELOPE', 12.5, 9.5, 0, 70, 7.55],
            ['PM-FR-SmBox', 'Priority Mail Small Flat Rate Box', 'PRIORITY COMMERCIAL', '', 'SM FLAT RATE BOX', 8.625, 5.375, 1.625, 70, 7.50],
            ['PM-FR-MdBox1', 'Priority Mail Medium Flat Rate Box', 'PRIORITY COMMERCIAL', '', 'MD FLAT RATE BOX', 11, 8.5, 5.5, 70, 12.8, 17.60, 12.80],
            ['PM-FR-MdBox2', 'Priority Mail Medium Flat Rate Box', 'PRIORITY COMMERCIAL', '', 'MD FLAT RATE BOX', 13.625, 11.875, 3.375, 70, 12.80],
            ['PM-FR-LgBox', 'Priority Mail Large Flat Rate Box', 'PRIORITY COMMERCIAL', '', 'LG FLAT RATE BOX', 12, 12, 5.5, 70, 17.60],
            ['PM-FR-BgBox', 'Priority Mail Large Flat Rate Board Game Box', 'PRIORITY COMMERCIAL', '', 'LG FLAT RATE BOX', 23.687, 11.75, 3, 70, 17.60],
        ];

    /**
     * @var bool
     */
    protected $hasDefinedCost = true;

    /**
     * @var float
     */
    protected $price;

    /**
     * @inheritDoc
     */
    public function __construct(string $shortName)
    {
        parent::__construct($shortName);

        foreach (static::USPS_BOXES as $value) {
            if ($value[0] === $shortName) {
                $this->price = $value[9];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        if (!$this->canBeShipped($shippingRequest)) {
            return 'Can not be shipped';
        }

        return $this->price;
    }
}
