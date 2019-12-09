<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Usps\Datastore;

use rollun\Usps\Datastore\UspsRates;
use rollun\datastore\DataStore\Interfaces\DataStoreInterface;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\SortNode;
use rollun\Usps\Datastore\UspsRates as DatastoreUspsRates;
use rollun\Usps\ShippingDataManager;

/**
 * http://service-usps.loc/api/datastore/rm-prodno-price?PRODNO=123&QTY=2
 */
class RmatvProdNoUspsRate extends UspsRates
{

    /**
     * Rmatv FTP Price fild => USPS API fild
     */
    const FILDS = [
        'PRODNO' => null,
        'UPC' => null,
        'MF_ID' => null,
        'MSRP' => null,
        'DEALER_PRICE' => null,
        'NAME' => null,
        'QTY_UT' => null,
        'QTY_KY' => null,
        'KIT_QTY' => null,
        'WEIGHT' => 'Pounds',
        'DEPTH' => 'Length',
        'HEIGHT' => 'Height',
        'WIDTH' => 'Width',
        'DISCONTINUE' => null,
        'PICTURE' => null,
        'BRAND' => null,
        'COLOR' => null,
        'SIZE' => null,
        'ORMD' => null,
        'NO_EXPORT' => null,
        'SPECIAL_ORD' => null,
        'OVERSIZE' => null,
        'NOTE' => null,
        'RMATV_PRICE' => null
    ];

    /**
     *
     * @var DataStoreInterface   Rmatv FTP Price
     */
    protected $rmFtpPrice;
    protected $zipOrigination;
    protected $zipDestination;

    public function __construct(DataStoreInterface $rmFtpPrice, $zipOrigination, $zipDestination)
    {
        $this->rmFtpPrice = $rmFtpPrice;
        $this->zipOrigination = (string) $zipOrigination;
        $this->zipDestination = (string) $zipDestination;
    }

    /**
     * www.site.com/RmatvProdNoUspsRate?PRODNO=123
     * www.site.com/RmatvProdNoUspsRate?PRODNO=123&QTY=2
     */
    public function query(Query $query)
    {
        $data ['ZipOrigination'] = $this->zipOrigination;
        $data ['ZipDestination'] = $this->zipDestination;

        $nodeQuery = $query->getQuery();
        $params = $this->getNodeQueryParams($nodeQuery);
        $prodno = $params['PRODNO'];
        $qty = $params['QTY'] ?? 1;

        $rmFtpPriceQquery = new Query();
        $rmFtpPriceQquery->setQuery(new EqNode('PRODNO', $prodno));
        $prodnoRecs = $this->rmFtpPrice->query($rmFtpPriceQquery);
        if ($prodnoRecs === []) {
            throw new \InvalidArgumentException("There is not item with Prodno = $prodno in RM ATV price");
        }
        $prodnoRec = $prodnoRecs[0];

        $data['Pounds'] = $this->getPounds($prodnoRec['WEIGHT'], $qty);
        $data['Ounces'] = 0;

        $oneItemDimensions = [$prodnoRec['DEPTH'], $prodnoRec['HEIGHT'], $prodnoRec['WIDTH']];
        $dimensions = $this->getPackDimensions($oneItemDimensions, $qty);

        $data['Width'] = $dimensions[0];
        $data['Length'] = $dimensions[1];
        $data['Height'] = $dimensions[2] * $qty;
        $retesDataStore = $this->getSippingPrices($data);

        $bestRateQuery = new Query();
        $sort = new SortNode();
        $sort->addField('Price', SortNode::SORT_ASC);
        $bestRateQuery->setSort($sort);

        $bestPriceRecs = $retesDataStore->query($bestRateQuery);
        $bestPriceRec = $bestPriceRecs[0];
        return $bestPriceRec;
    }

    protected function getNodeQueryParams(AbstractQueryNode $nodeQuery)
    {
        $nodeName = $nodeQuery->getNodeName();
        if ($nodeName === 'eq' && $nodeQuery->getField() === 'PRODNO') {
            return ['PRODNO' => $nodeQuery->getValue()];
        }
        if ($nodeName !== 'and') {
            throw new \InvalidArgumentException('Query must has node "and" like PRODNO=123&QTY=2');
        }
        /* @var $innerQuery AndNode  */
        $andQueries = $nodeQuery->getQueries();
        foreach ($andQueries as $node) {
            /* @var $node AbstractScalarOperatorNode  */
            if ('eq' === $node->getNodeName() && in_array($node->getField(), ['PRODNO', 'QTY'])) {
                $params[$node->getField()] = $node->getValue();
            }
        }

        if (!(is_array($params) && array_key_exists('PRODNO', $params))) {
            throw new \InvalidArgumentException('There is not  PRODNO in query');
        }
        return $params;
    }

    public function getPounds($weight, $qty)
    {
        return $weight * $qty;
    }

    public function getPackDimensions($oneItemDimensions, $qty, $packingFactor = 2, $lastPackingFactor = 2)
    {
        if ($qty === 1) {
            $packDimensionsList = $oneItemDimensions;
        } else {
            rsort($oneItemDimensions, SORT_NUMERIC);
            $max = $oneItemDimensions[0];
            $mid = $oneItemDimensions[1];
            $min = $oneItemDimensions[2];

            $dividers = [7, 5, 3, 2];
            do {
                $divider = array_shift($dividers);
                $maxDivider = (
                        (ceil($qty / $divider) === $qty / $divider) && ($max >= $divider * $min)
                        ) ? $divider : null;
            } while (isset($maxDivider) || empty($dividers));
            $maxDivider = $maxDivider ?? $packingFactor;

            if ($qty > $lastPackingFactor) {  //3?
                return $this->getPackDimensions([$max, $mid, $maxDivider * $min], ceil($qty / $maxDivider));
            }

            $packDimensionsList = [$max, $mid, $qty * $min];
        }
        rsort($packDimensionsList, SORT_NUMERIC);
        $packDimensions['Width'] = $packDimensionsList[0];
        $packDimensions['Length'] = $packDimensionsList[1];
        $packDimensions['Height'] = $packDimensionsList[2];

        return $packDimensions;
    }
}
