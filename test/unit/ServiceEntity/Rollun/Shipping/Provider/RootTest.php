<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace service\test\unit\Entity\Rollun\Shipping\Method\Provider;

use PHPUnit\Framework\TestCase;
use service\Entity\Rollun\Shipping\Method\Provider\Root as RootProvider;

class RootTest extends TestCase
{

    public function testAppend()
    {
        global $container;
        $rootProvider = $container->get('Root');

        $this->assertEquals(
                RootProvider::class, get_class($rootProvider)
        );
    }
}
