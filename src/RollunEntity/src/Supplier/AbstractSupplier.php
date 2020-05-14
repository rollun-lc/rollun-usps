<?php
declare(strict_types=1);

namespace rollun\Entity\Supplier;

use rollun\dic\InsideConstruct;
use rollun\Entity\Product\Item\ItemInterface;
use service\Entity\Api\DataStore\Shipping\AllCosts;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Query;

/**
 * Class AbstractSupplier
 *
 * @author r.ratsun <r.ratsun.rollun@gmail.com>
 */
abstract class AbstractSupplier
{
    /**
     * @var AllCosts
     */
    protected $allCosts;

    /**
     * @var null|string
     */
    protected $zipOriginal = null;

    /**
     * @var null|array
     */
    protected $shippingMethods = null;

    /**
     * AbstractSupplier constructor.
     *
     * @param AllCosts|null $allCosts
     *
     * @throws \ReflectionException
     */
    public function __construct(AllCosts $allCosts = null)
    {
        InsideConstruct::init(
            [
                'allCosts' => AllCosts::class
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
                'allCosts' => AllCosts::class
            ]
        );
    }

    /**
     * @param ItemInterface $item
     * @param string        $zipDestination
     *
     * @return array|null
     */
    public function getBestShippingMethod(ItemInterface $item, string $zipDestination): ?array
    {
        // get all available shipping methods
        $shippingMethods = $this->allCosts->query($this->buildShippingQuery($item, $zipDestination));

        if (empty($shippingMethods) || !is_array($shippingMethods)) {
            return null;
        }

        foreach ($this->getShippingMethods() as $supplierShippingMethod) {
            foreach ($shippingMethods as $shippingMethod) {
                if (strpos($shippingMethod['id'], $supplierShippingMethod['name']) !== false && $this->isValid($item, $zipDestination, $supplierShippingMethod['name'])) {
                    return $shippingMethod;
                }
            }
        }

        return null;
    }

    /**
     * @param ItemInterface $item
     * @param string        $zipDestination
     * @param string        $shippingMethod
     *
     * @return bool
     */
    protected function isValid(ItemInterface $item, string $zipDestination, string $shippingMethod): bool
    {
        return true;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getZipOriginal(): string
    {
        if (empty($this->zipOriginal) || !is_string($this->zipOriginal)) {
            throw new \Exception('No valid zipOriginal for Supplier class');
        }

        return $this->zipOriginal;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getShippingMethods(): array
    {
        if (empty($this->shippingMethods) || !is_array($this->shippingMethods)) {
            throw new \Exception('No valid shippingMethods for Supplier class');
        }

        return $this->shippingMethods;
    }

    /**
     * @param ItemInterface $item
     * @param string        $zipDestination
     *
     * @return Query
     * @throws \Exception
     */
    protected function buildShippingQuery(ItemInterface $item, string $zipDestination): Query
    {
        // get item dimensions
        $dimensions = $item->getDimensionsList()[0]['dimensions'];

        $query = new Query();
        $andNode = new AndNode(
            [
                new EqNode('ZipOrigination', $this->getZipOriginal()),
                new EqNode('ZipDestination', $zipDestination),
                new EqNode('Pounds', $item->getWeight()),
                new EqNode('Width', $dimensions->max),
                new EqNode('Length', $dimensions->mid),
                new EqNode('Height', $dimensions->min),
                new EqNode('Error', null),
                new NeNode('cost', null),
                new EqNode('Quantity', 1)
            ]
        );

        $query->setQuery($andNode);
        $query->setSort(new SortNode(['cost' => SortNode::SORT_ASC]));

        return $query;
    }
}
