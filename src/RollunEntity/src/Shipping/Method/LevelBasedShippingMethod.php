<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class LevelBasedShippingMethod
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
abstract class LevelBasedShippingMethod extends ShippingMethodAbstract
{
    /**
     * @var array
     */
    protected $levels = [];

    /**
     * LevelBasedShippingMethod constructor.
     *
     * @param string $shortName
     * @param array  $levels
     */
    public function __construct(string $shortName, array $levels = null)
    {
        $this->shortName = $shortName;

        if ($levels !== null) {
            $this->levels = $levels;
        }
    }

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        if ($this->canBeShipped($shippingRequest) && !empty($levels = $this->getLevels())) {
            foreach ($levels as $level) {
                if ($this->isLevelValid($shippingRequest, $level)) {
                    return $this->getLevelCost($level);
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getLevels(): array
    {
        return $this->levels;
    }

    /**
     * @param ShippingRequest $shippingRequest
     * @param array           $level
     *
     * @return bool
     */
    abstract protected function isLevelValid(ShippingRequest $shippingRequest, array $level): bool;

    /**
     * @param array $level
     *
     * @return float|null
     */
    abstract protected function getLevelCost(array $level): ?float;
}
