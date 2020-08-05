<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Usps\Datastore;

use rollun\datastore\DataStore\Traits\NoSupportCountTrait;
use rollun\datastore\DataStore\Traits\NoSupportCreateTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteAllTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteTrait;
use rollun\datastore\DataStore\Traits\NoSupportHasTrait;
use rollun\datastore\DataStore\Traits\NoSupportIteratorTrait;
use rollun\datastore\DataStore\Traits\NoSupportReadTrait;
use rollun\datastore\DataStore\Traits\NoSupportUpdateTrait;
use rollun\datastore\DataStore\Memory;
use rollun\datastore\DataStore\DataStoreAbstract;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use rollun\Usps\ShippingData;
use rollun\Usps\ShippingDataManager;
use rollun\Usps\ShippingPriceCommercial;

class UspsRates extends DataStoreAbstract
{

    /**
     *
     * @var Memory
     */
    protected $memoryDataStore;

    use NoSupportCreateTrait,
        NoSupportDeleteAllTrait,
        NoSupportDeleteTrait,
        NoSupportHasTrait,
        NoSupportIteratorTrait,
        NoSupportReadTrait,
        NoSupportUpdateTrait,
        NoSupportCountTrait;

    /**
     * {@inheritdoc}
     */
    public function query(Query $query)
    {
        $innerQuery = $query->getQuery();
        $nodeName = $innerQuery->getNodeName();
        if ($nodeName !== 'and') {
            throw new \InvalidArgumentException('Query must has node "and" with package params');
        }
        /* @var $innerQuery AndNode  */
        $andQueries = $innerQuery->getQueries();

        foreach ($andQueries as $node) {
            /* @var $node AbstractScalarOperatorNode  */
            if ('eq' === $node->getNodeName() && in_array($node->getField(), ShippingData::ORDER)) {
                $data[$node->getField()] = $node->getValue();
            }
        }

        $memoryDataStore = $this->getSippingPrices($data);
        return $memoryDataStore->query($query);
    }

    public function getSippingPrices($data)
    {
        $shippingData = new ShippingData($data);

        $shippingDataManager = new ShippingDataManager($shippingData);
        $arrayOfShippingData = $shippingDataManager->getArrayOfShippingData();

        $shippingPrice = new ShippingPriceCommercial($shippingData);
        $responseData = $shippingPrice->getShippingPrice($arrayOfShippingData);

        if (array_key_exists('Error', $responseData)) {
            throw new \InvalidArgumentException($responseData['Error']);
        }

        $memoryDataStore = new Memory();
        foreach ($responseData as $key => $package) {
            $clicknshipServiceType = $this->clicknshipServiceType($package, $arrayOfShippingData[$key]);
            $record = array_merge($package, [
                'id' => $clicknshipServiceType,
                'cAndShipServiceType' => $clicknshipServiceType]);
            $record = array_merge($arrayOfShippingData[$key]->data, $record);
            $memoryDataStore->create($record);
        }

        return $memoryDataStore;
    }

    public function clicknshipServiceType($record, $shippingData)
    {
        if ($record['Service'] === 'FIRST CLASS COMMERCIAL') {
            return 'First-Class Package Service';
        }
        if ($record['Service'] !== 'PRIORITY COMMERCIAL') {
            throw new \InvalidArgumentException('Wrong Service type: ' . $record['Service']);
        }
        switch ($record['Container']) {
            case 'CUBIC PARCELS':
                return 'Priority Mail Cubic';
            case 'VARIABLE':
                return 'Priority Mail';
            case 'FLAT RATE ENVELOPE':
                return 'Priority Mail Flat Rate Envelope';
            case 'LEGAL FLAT RATE ENVELOPE':
                return 'Priority Mail Legal Flat Rate Envelope';
            case 'PADDED FLAT RATE ENVELOPE':
                return 'Priority Mail Flat Rate Padded Envelope';
            case 'SM FLAT RATE BOX':
                return 'Priority Mail Small Flat Rate Box';
            case 'MD FLAT RATE BOX':
                return 'Priority Mail Medium Flat Rate Box';
            case 'LG FLAT RATE BOX':
                if (12 < max($shippingData['Width'], $shippingData['Length'], $shippingData['Height'])) {
                    return 'Priority Mail Large Flat Rate Board Game Box';
                } else {
                    return 'Priority Mail Large Flat Rate Box';
                }
            case 'REGIONAL RATE BOX A':
                return 'Priority Mail Regional Rate Box A';
            case 'REGIONAL RATE BOX B':
                return 'Priority Mail Regional Rate Box B';

            default:
                throw new \InvalidArgumentException('Wrong Container type: ' . $record['Container']);
        }
    }
}
