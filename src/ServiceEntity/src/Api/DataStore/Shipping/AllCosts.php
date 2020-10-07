<?php
declare(strict_types=1);

namespace service\Entity\Api\DataStore\Shipping;

use Jaeger\Tag\StringTag;
use Jaeger\Tracer\Tracer;
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
use rollun\utils\Json\Serializer;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use rollun\Entity\Usps\ShippingData;
use rollun\Entity\Shipping\ShippingRequest;
use service\Entity\Rollun\Shipping\Method\Provider\Root as RootProvider;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\dic\InsideConstruct;
use Psr\Log\LoggerInterface;
use rollun\Entity\Subject\Address;


/**
 * Class AllCosts
 *
 * @example http://service-usps.loc/api/datastore/shipping-all-costs?and(eq(ZipOrigination,10005),eq(ZipDestination,91730),eq(Width,2),eq(Length,2),eq(Height,5),eq(Pounds,2),ne(cost,null()))&sort(+cost)&limit(50)
 * @example http://service-usps.loc/api/datastore/shipping-all-costs?ZipOrigination=91601&ZipDestination=91730&Width=1&Length=10&Height=5&Pounds=0.5&Click_N_Shipp=Priority%20Mail
 * @example http://service-usps.loc/api/datastore/shipping-all-costs?ZipOrigination=91601&ZipDestination=91730&Width=1&Length=10&Height=5&Pounds=1&like(id,*FtCls*)&limit(2,1)&select(id)
 * @example http://service-usps.loc/api/datastore/shipping-all-costs?and(eq(ZipOrigination,28790),eq(ZipDestination,91730),eq(Width,2),eq(Length,2),eq(Height,1),eq(Pounds,2),eq(attr_CommodityCode,301),ne(cost,null()))&sort(+cost)&limit(50)
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class AllCosts extends DataStoreAbstract
{
    /**
     * Define attributes query prefix
     */
    const ATTRIBUTE_PREFIX = 'attr_';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Tracer
     */
    protected $tracer;

    /**
     *
     * @var RootProvider
     */
    protected $root;

    use NoSupportCreateTrait,
        NoSupportDeleteAllTrait,
        NoSupportDeleteTrait,
        NoSupportHasTrait,
        NoSupportIteratorTrait,
        NoSupportReadTrait,
        NoSupportUpdateTrait,
        NoSupportCountTrait;

    /**
     * LoggerHandler constructor.
     * @param RootProvider|null $root
     * @param LoggerInterface|null $logger
     * @param Tracer|null $tracer
     * @throws \ReflectionException
     */
    public function __construct(RootProvider $root = null, LoggerInterface $logger = null, Tracer $tracer = null)
    {
        InsideConstruct::init([
            'root' => 'Root',
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class,
        ]);
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup([
            'root' => 'Root',
            'logger' => LoggerInterface::class,
            'tracer' => Tracer::class,
        ]);
    }

    public function __sleep()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function query(Query $query)
    {
        $span = $this->tracer->start('AllCosts.query', [
            new StringTag('query', Serializer::jsonSerialize($query)),
        ]);

        $innerQuery = $query->getQuery();

        $nodeName = is_null($innerQuery) ? null : $innerQuery->getNodeName();
        if (is_null($nodeName) || $nodeName !== 'and') {
            throw new \InvalidArgumentException('Query must has node "and" with package params');
        }
        /* @var $innerQuery AndNode */
        $andQueries = $innerQuery->getQueries();
        $queryParams = ['Attributes' => []];
        $addQuery = [];
        foreach ($andQueries as $node) {
            /* @var $node AbstractScalarOperatorNode */
            if ('eq' === $node->getNodeName() && in_array($node->getField(), ShippingData::ORDER)) {
                $queryParams[$node->getField()] = $node->getValue();
            } elseif ($node->getField() === 'Quantity') {
                $queryParams[$node->getField()] = $node->getValue();
            } elseif ('eq' === $node->getNodeName() && $this->isAttribute($node->getField())) {
                $queryParams['Attributes'][str_replace(self::ATTRIBUTE_PREFIX, '', $node->getField())] = $node->getValue();
            } else {
                $addQuery[] = $node;
            }
        }

        if (!empty($queryParams['Ounces'])) {
            $queryParams['Pounds'] = $queryParams['Pounds'] + $queryParams['Ounces'] / 16;
            unset($queryParams['Ounces']);
        }

        $shippingRequest = $this->makeShippingRequest($queryParams);
        $responseSet = $this->root->getShippingMethods($shippingRequest);

        $span->addTag(new StringTag('responseSet', json_encode($responseSet)));


        $memoryDataStore = new Memory();
        foreach ($responseSet as $key => $record) {
            $memoryDataStore->create($record);
        }

        $outputQuery = $this->outputQuery($query, $addQuery);
        $result = $memoryDataStore->query($outputQuery);

        $span->addTag(new StringTag('result', json_encode($result)));


        $this->tracer->finish($span);
        return $result;
    }

    protected function makeShippingRequest(array $queryParams)
    {
        $span = $this->tracer->start('AllCosts.makeShippingRequest', [
            new StringTag('queryParams', json_encode($queryParams)),
        ]);

        $addressOrigination = new Address('', $queryParams['ZipOrigination']);
        $addressDestination = new Address('', $queryParams['ZipDestination']);

        $rectangular = new Rectangular($queryParams['Width'], $queryParams['Length'], $queryParams['Height']);

        if (isset($queryParams['Quantity']) && ($queryParams['Quantity'] === 2 || $queryParams['Quantity'] === 4)) {
            $rectangular->min *= 2;
            if ($queryParams['Quantity'] === 4) {
                $rectangular->min *= 2;
            }
            $queryParams['Pounds'] *= $queryParams['Quantity'];
        }
        $product = new Product($rectangular, $queryParams['Pounds']);
        $shippingRequest = new ShippingRequest($product, $addressOrigination, $addressDestination, $queryParams['Attributes']);


        $span->addTag(new StringTag('addressOrigination', json_encode($addressOrigination)));
        $span->addTag(new StringTag('addressDestination', json_encode($addressDestination)));
        $span->addTag(new StringTag('rectangular', json_encode($rectangular)));
        $span->addTag(new StringTag('product', json_encode($product)));

        $this->tracer->finish($span);
        return $shippingRequest;
    }

    protected function outputQuery(Query $query, array $addQuery)
    {
        $span = $this->tracer->start('AllCosts.outputQuery', [
            new StringTag('query', Serializer::jsonSerialize($query)),
            new StringTag('addQuery', json_encode($addQuery)),
        ]);

        $outputQuery = new Query();
        if (!empty($addQuery)) {
            $andNode = new AndNode($addQuery);
            $outputQuery->setQuery($andNode);
        }
        if (!empty($query->getSelect())) {
            $outputQuery->setSelect($query->getSelect());
        }
        if (!empty($query->getLimit())) {
            $outputQuery->setLimit($query->getLimit());
        }
        if (!empty($query->getSort())) {
            $outputQuery->setSort($query->getSort());
        }

        $span->addTag(new StringTag('outputQuery', Serializer::jsonSerialize($outputQuery)));
        $this->tracer->finish($span);
        return $outputQuery;
    }

    public function buildUspShippingQuery($zipOrigination, $zipDestination, $pounds, $width, $length, $height, $quantity = null)
    {
        $query = new Query();
        $andNode = new AndNode([
            new EqNode('ZipOrigination', $zipOrigination),
            new EqNode('ZipDestination', $zipDestination),
            new EqNode('Pounds', $pounds),
            new EqNode('Width', $width),
            new EqNode('Length', $length),
            new EqNode('Height', $height),
            new EqNode('Error', null),
            new NeNode('cost', null),
//            new EqNode('attr_dangerous', 'true'), // указывает на то опасный ли товар, если да то нельзя отправлять самолетом
        ]);
        if ($quantity) {
            $andNode->addQuery(new EqNode('Quantity', $quantity));
        }

        $query->setQuery($andNode);
        $query->setSort(new SortNode(['cost' => SortNode::SORT_ASC]));
//        $query->setLimit(new LimitNode(1));
        return $query;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    private function isAttribute(string $field): bool
    {
        return strpos($field, self::ATTRIBUTE_PREFIX) === 0 && count($parts = explode(self::ATTRIBUTE_PREFIX, $field)) === 2 && !empty($parts[1]);
    }
}
