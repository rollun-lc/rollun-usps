<?php
declare(strict_types=1);

namespace rollun\HowToBuy\Shipping\Method\Usps;

use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;
use rollun\HowToBuy\Api\DataStore\Shipping\BestShipping;

/**
 * Class PriorityMailCovid19
 *
 * Temporary shipping method
 *
 * @author r.ratsun <r.ratsun.rollun@gmail.com>
 */
class PriorityMailCovid19 extends ShippingsAbstract
{
    /**
     * @var array
     */
    protected $mapping
        = [
            'Usps-PM-FR-Env-COVID19'      => 'Priority Mail Flat Rate Envelope',
            'Usps-PM-FR-LegalEnv-COVID19' => 'Priority Mail Legal Flat Rate Envelope',
            'Usps-PM-FR-Pad-Env-COVID19'  => 'Priority Mail Flat Rate Padded Envelope',
            'Usps-PM-COVID19'             => 'Priority Mail',
        ];

    /**
     * @var null|float
     */
    protected $price = null;

    /**
     * PriorityMailAvailable constructor.
     *
     * @param string $shortName
     */
    public function __construct(string $shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        if (isset($this->mapping[$this->shortName]) && !empty($csn = $shippingRequest->getAttribute('csn'))) {
            $data = BestShipping::httpSend("api/datastore/RockyMountainUSPSPriorityMailAvailable?eq(supplier_id,$csn)&limit(1,0)");
            if (isset($data[0]['shipping_type']) && isset($data[0]['price'])) {
                if ($data[0]['shipping_type'] === $this->mapping[$this->shortName]) {
                    if ($data[0]['shipping_type'] == 'Priority Mail') {
                        $zone = $this->getZone($shippingRequest->getOriginationZipCode(), $shippingRequest->getDestinationZipCode());
                        if ($zone > 5) {
                            return false;
                        }
                    }

                    $this->price = $data[0]['price'];

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        if (!$this->canBeShipped($shippingRequest)) {
            return null;
        }

        return $this->price;
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return array
     */
    public function getAddData(ShippingRequest $shippingRequest): array
    {
        return [
            'name' => $this->mapping[$this->shortName]
        ];
    }
}
