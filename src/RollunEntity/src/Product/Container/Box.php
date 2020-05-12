<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Product\Container;

use OpenAPI\Client\Api\PackerApi;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\ItemInterface;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Product\Item\ProductPack;
use OpenAPI\Client\Model\Result;

class Box extends ContainerAbstract
{

    const TYPE_BOX = 'Box';

    public $max;
    public $mid;
    public $min;

    public function __construct($max, $mid, $min)
    {
        $dim = compact('max', 'mid', 'min');
        rsort($dim, SORT_NUMERIC);
        [$this->max, $this->mid, $this->min] = $dim;
    }

    protected function canFitProduct(ItemInterface $item): bool
    {
        $dimensionsList = $item->getDimensionsList();
        $dimensions = $dimensionsList[0]['dimensions'];
        return

            $this->max >= $dimensions->max &&
            $this->mid >= $dimensions->mid &&
            $this->min >= $dimensions->min;
    }

    /**
     * @inheritDoc
     */
    protected function canFitProductPack(ItemInterface $item): bool
    {
//        return count($this->pack($item)->getContainers()) === 1;

        $dimensionsList = $item->getDimensionsList();
        $dimensions = $dimensionsList[0]['dimensions'];

        if ($this->max < $dimensions->max || $this->mid < $dimensions->mid || $this->min < $dimensions->min) {
            return false;
        }

        // find max possible quantity
        $maxQuantity = 0;
        foreach ($this->arrayCombinations([$dimensions->max, $dimensions->mid, $dimensions->min]) as $row) {
            $itemDimensions = explode("-", $row);

            $rowQuantity = intdiv($this->max, (int)$itemDimensions[0]) * intdiv($this->mid, (int)$itemDimensions[1]) * intdiv($this->min, (int)$itemDimensions[2]);

            if ($rowQuantity > $maxQuantity){
                $maxQuantity = $rowQuantity;
            }
        }

        return $item->quantity <= $maxQuantity;
    }

    public function getType(): string
    {
        return static::TYPE_BOX;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function arrayCombinations(array $data): array
    {
        if (count($data) <= 1) {
            $result = $data;
        } else {
            $result = [];
            for ($i = 0; $i < count($data); ++$i) {
                $firstword = $data[$i];
                $input = [];
                for ($j = 0; $j < count($data); ++$j) {
                    if ($i <> $j) {
                        $input[] = $data[$j];
                    }
                }
                $combos = $this->arrayCombinations($input);
                for ($j = 0; $j < count($combos); ++$j) {
                    $result[] = $firstword . '-' . $combos[$j];
                }
            }
        }

        return $result;
    }

    /**
     * @param ItemInterface $item
     *
     * @return Result
     * @throws \OpenAPI\Client\ApiException
     */
    protected function pack(ItemInterface $item): Result
    {
        // get item dimensions
        $dimensions = $item->getDimensionsList()[0]['dimensions'];

        // prepare data
        $data = [
            'container' => [
                'name'      => 'no-name',
                'price'     => 1,
                'width'     => $this->max,
                'height'    => $this->mid,
                'length'    => $this->min,
                'thickness' => 0,
            ],
            'item'      => [
                'name'     => 'no-name',
                'width'    => $dimensions->max,
                'height'   => $dimensions->mid,
                'length'   => $dimensions->min,
                'quantity' => $item->quantity,
            ]
        ];

        // prepare request body
        $body = new \OpenAPI\Client\Model\Body();
        $body->setContainer([new \OpenAPI\Client\Model\Container($data['container'])]);
        $body->setItems([new \OpenAPI\Client\Model\Item($data['item'])]);

        return (new PackerApi(new \GuzzleHttp\Client()))->pack($body);
    }
}
