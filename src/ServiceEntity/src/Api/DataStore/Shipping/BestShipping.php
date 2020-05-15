<?php
declare(strict_types=1);

namespace service\Entity\Api\DataStore\Shipping;

use rollun\datastore\DataStore\DataStoreAbstract;
use rollun\datastore\DataStore\Traits\NoSupportCountTrait;
use rollun\datastore\DataStore\Traits\NoSupportCreateTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteAllTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteTrait;
use rollun\datastore\DataStore\Traits\NoSupportHasTrait;
use rollun\datastore\DataStore\Traits\NoSupportIteratorTrait;
use rollun\datastore\DataStore\Traits\NoSupportReadTrait;
use rollun\datastore\DataStore\Traits\NoSupportUpdateTrait;
use rollun\dic\InsideConstruct;
use rollun\Entity\Supplier\AbstractSupplier;
use rollun\Entity\Supplier\AutoDist;
use rollun\Entity\Supplier\PartsUnlimited;
use rollun\Entity\Supplier\RockyMountain;
use rollun\Entity\Supplier\Slt;
use Xiag\Rql\Parser\Query;

/**
 * Class BestShipping
 *
 * @author r.ratsun <r.ratsun.rollun@gmail.com>
 */
class BestShipping extends DataStoreAbstract
{
    use NoSupportCreateTrait;
    use NoSupportDeleteAllTrait;
    use NoSupportDeleteTrait;
    use NoSupportHasTrait;
    use NoSupportIteratorTrait;
    use NoSupportReadTrait;
    use NoSupportUpdateTrait;
    use NoSupportCountTrait;

    /**
     * Required request params
     */
    const REQUIRED_PARAMS = ['ZipDestination', 'RollunId'];

    /**
     * Sorted suppliers array
     */
    protected $suppliers
        = [
            'PartsUnlimited' => 'partsUnlimited',
            'RockyMountain'  => 'rockyMountain',
            'Slt'            => 'slt',
            'Autodist'       => 'autoDist',
        ];

    /**
     * @var PartsUnlimited
     */
    protected $partsUnlimited;

    /**
     * @var RockyMountain
     */
    protected $rockyMountain;

    /**
     * @var Slt
     */
    protected $slt;

    /**
     * @var AutoDist
     */
    protected $autoDist;

    /**
     * BestShipping constructor.
     *
     * @param PartsUnlimited|null $partsUnlimited
     * @param RockyMountain|null  $rockyMountain
     * @param Slt|null            $slt
     * @param AutoDist|null       $autoDist
     *
     * @throws \ReflectionException
     */
    public function __construct(PartsUnlimited $partsUnlimited = null, RockyMountain $rockyMountain = null, Slt $slt = null, AutoDist $autoDist = null)
    {
        InsideConstruct::init(
            [
                'partsUnlimited' => PartsUnlimited::class,
                'rockyMountain'  => RockyMountain::class,
                'slt'            => Slt::class,
                'autoDist'       => AutoDist::class,
            ]
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function __wakeup()
    {
        InsideConstruct::initWakeup(
            [
                'partsUnlimited' => PartsUnlimited::class,
                'rockyMountain'  => RockyMountain::class,
                'slt'            => Slt::class,
                'autoDist'       => AutoDist::class,
            ]
        );
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function query(Query $query)
    {
        // prepare and validate query params
        $queryParams = $this->getQueryParams($query);

        // get all suppliers for product
        $supplierMapping = AbstractSupplier::httpSend("api/datastore/SupplierMappingDataStore?eq(rollun_id,{$queryParams['RollunId']})&limit(20,0)");

        $bestShipping = [];

        // find supplier
        foreach ($this->suppliers as $supplierName => $service) {
            foreach ($supplierMapping as $v) {
                if ($v['supplier_name'] == $supplierName && $this->$service->isInStock($queryParams['RollunId'])) {
                    /** @var AbstractSupplier $supplier */
                    $supplier = $this->$service;

                    $bestShipping[] = $supplier->getBestShippingMethod($supplier->createItem($queryParams['RollunId']), (string)$queryParams['ZipDestination']);
                }
            }
        }

        if (empty($bestShipping[0])) {
            return [];
        }

        usort($bestShipping, [$this, 'cmpResult']);

        return $bestShipping;
    }

    /**
     * @param Query $query
     *
     * @return array
     */
    protected function getQueryParams(Query $query): array
    {
        $innerQuery = $query->getQuery();
        $nodeName = is_null($innerQuery) ? null : $innerQuery->getNodeName();
        if (is_null($nodeName) || $nodeName !== 'and') {
            throw new \InvalidArgumentException("Query must has node 'and' with package params");
        }

        $queryParams = [];
        foreach ($innerQuery->getQueries() as $node) {
            if ('eq' === $node->getNodeName() && in_array($node->getField(), self::REQUIRED_PARAMS)) {
                $queryParams[$node->getField()] = $node->getValue();
            }
        }
        foreach (self::REQUIRED_PARAMS as $param) {
            if (empty($queryParams[$param])) {
                throw new \InvalidArgumentException("Param '$param' is required");
            }
        }

        return $queryParams;
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    protected function cmpResult(array $a, array $b): int
    {
        if ($a['priority'] == $b['priority']) {
            return 0;
        }
        return ($a['priority'] < $b['priority']) ? -1 : 1;
    }
}
