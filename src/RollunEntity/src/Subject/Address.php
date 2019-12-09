<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Subject;

class Address
{

    protected $addressLine;
    public $zip5 = '';
    public $zip4;

    /**
     *
     * @param string $addressLine
     * @param string $zipCode 12345 or 12345-1234
     */
    public function __construct($addressLine = null, $zipCode = null)
    {
        $this->setAddressLine($addressLine);
        if (!empty($zipCode)) {
            $this->setZipCode($zipCode);
        }
    }

    public function setAddressLine($addressLine)
    {
        $this->addressLine = $addressLine;
    }

    public function getAddressLine()
    {
        return $this->addressLine;
    }

    public function setZipCode($zipCode)
    {

        if (strpos((string) $zipCode, "-") === 5) {
            $this->zip4 = substr((string) $zipCode, 6, 4);
            $this->zip5 = substr((string) $zipCode, 0, 5);
        } else {
            $zip5 = substr((string) (100000 + $zipCode), 1, 5);
            $this->zip5 = $zip5 == '00000' ? "" : $zip5;
        }
    }

    public function getZipCode()
    {
        if (empty($this->zip5)) {
            return "";
        }
        if (empty($this->zip4)) {
            return $this->zip5;
        }
        return $this->zip5 . "-" . $this->zip4;
    }
}
