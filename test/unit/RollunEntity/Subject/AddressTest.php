<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\test\unit\Entity\Subject;

use PHPUnit\Framework\TestCase;
use rollun\Entity\Subject\Address;

class AddressTest extends TestCase
{

    public function testProcessFullZip()
    {

        $address = new Address('', '12345-6789');
        $this->assertEquals(
                12345, $address->zip5
        );
        $this->assertEquals(
                6789, $address->zip4
        );
        $this->assertEquals(
                '12345-6789', $address->getZipCode()
        );
    }

    public function testProcessZip()
    {

        $address = new Address('', '12345');
        $this->assertEquals(
                12345, $address->zip5
        );
        $this->assertEquals(
                '', $address->zip4
        );
        $this->assertEquals(
                '12345', $address->getZipCode()
        );
    }
}
