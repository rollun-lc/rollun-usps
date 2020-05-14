<?php
declare(strict_types=1);

namespace service\Entity\Api\DataStore\Shipping;

use rollun\callback\Callback\Http;
use rollun\datastore\DataStore\DataStoreAbstract;
use rollun\datastore\DataStore\Traits\NoSupportCountTrait;
use rollun\datastore\DataStore\Traits\NoSupportCreateTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteAllTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteTrait;
use rollun\datastore\DataStore\Traits\NoSupportHasTrait;
use rollun\datastore\DataStore\Traits\NoSupportIteratorTrait;
use rollun\datastore\DataStore\Traits\NoSupportReadTrait;
use rollun\datastore\DataStore\Traits\NoSupportUpdateTrait;
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

    const REQUIRED_PARAMS = ['ZipDestination', 'RollunId'];

    /**
     * @inheritDoc
     */
    public function query(Query $query)
    {
        // prepare and validate query params
        $queryParams = $this->getQueryParams($query);

        return [];
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
}
