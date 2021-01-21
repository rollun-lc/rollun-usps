<?php
declare(strict_types=1);

namespace rollun\Entity\Product\Container;

use rollun\Usps\OpenAPI\Client\Model\Item as ApiItem;
use rollun\Usps\OpenAPI\Client\Api\PackerApi;
use rollun\Usps\OpenAPI\Client\Model\Result;
use rollun\Entity\Product\Item\ItemInterface;

/**
 * Class Box
 *
 * @author    r.ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Box extends ContainerAbstract
{
    /**
     * Container type
     */
    const TYPE_BOX = 'Box';

    /**
     * @var float
     */
    public $max;

    /**
     * @var float
     */
    public $mid;

    /**
     * @var float
     */
    public $min;

    /**
     * Box constructor.
     *
     * @param float $max
     * @param float $mid
     * @param float $min
     */
    public function __construct($max, $mid, $min)
    {
        $dim = compact('max', 'mid', 'min');
        rsort($dim, SORT_NUMERIC);
        [$this->max, $this->mid, $this->min] = $dim;
    }

    public function getType(): string
    {
        return static::TYPE_BOX;
    }

    /**
     * @inheritDoc
     */
    protected function canFitProduct(ItemInterface $item): bool
    {
        // get item dimensions
        $dimensions = $item->getDimensionsList()[0]['dimensions'];

        return $this->max >= $dimensions->max && $this->mid >= $dimensions->mid && $this->min >= $dimensions->min;
    }

    /**
     * @inheritDoc
     */
    protected function canFitProductPack(ItemInterface $item): bool
    {
        // get item dimensions
        $dimensions = $item->getDimensionsList()[0]['dimensions'];

        $items = [
            new ApiItem(
                [
                    'name'     => 'no-name',
                    'width'    => $dimensions->max,
                    'height'   => $dimensions->mid,
                    'length'   => $dimensions->min,
                    'quantity' => $item->quantity,
                ]
            )
        ];

        // is can be packed ?
        if (empty($pack = $this->pack($items))) {
            return false;
        }

        return count($pack->getContainers()) === 1;
    }

    /**
     * @inheritDoc
     */
    protected function canFitProductKit(ItemInterface $item): bool
    {
        $items = [];
        foreach ($item->items as $product) {
            // get product dimensions
            $dimensions = $product->getDimensionsList()[0]['dimensions'];

            $items[] = new ApiItem(
                [
                    'name'     => 'no-name',
                    'width'    => $dimensions->max,
                    'height'   => $dimensions->mid,
                    'length'   => $dimensions->min,
                    'quantity' => isset($product->quantity) ? $product->quantity : 1,
                ]
            );
        }

        // is can be packed ?
        if (empty($pack = $this->pack($items))) {
            return false;
        }

        return count($pack->getContainers()) === 1;
    }

    /**
     * @param array $items
     *
     * @return Result|null
     */
    protected function pack(array $items): ?Result
    {
        $containerData = [
            'name'      => 'no-name',
            'price'     => 1,
            'width'     => $this->max,
            'height'    => $this->mid,
            'length'    => $this->min,
            'thickness' => 0,
        ];

        // prepare request body
        $body = (new \rollun\Usps\OpenAPI\Client\Model\Body())
            ->setContainer([new \rollun\Usps\OpenAPI\Client\Model\Container($containerData)])
            ->setItems($items);

        try {
            $result = (new PackerApi(new \GuzzleHttp\Client()))->pack($body);
        } catch (\rollun\Usps\OpenAPI\Client\ApiException $e) {
            $result = null;
        }

        return $result;
    }
}
