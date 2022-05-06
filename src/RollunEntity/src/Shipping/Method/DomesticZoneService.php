<?php

declare(strict_types=1);

namespace rollun\Entity\Shipping\Method;

class DomesticZoneService
{
    /**
     * @var string
     */
    private $domesticZoneApiMask;

    /**
     * @var string
     */
    private $cacheFile;

    public function __construct(string $domesticZoneApiMask, string $cacheFile)
    {
        $this->domesticZoneApiMask = $domesticZoneApiMask;
        $this->cacheFile = $cacheFile;
    }

    /**
     * @param string $zipFrom
     * @param string $zipTo
     *
     * @return int
     * @throws \Exception
     */
    public function getZone(string $zipFrom, string $zipTo): int
    {
        // get zones
        $data = $this->getDomesticZones($zipFrom);

        // prepare destinationZip
        $destinationZip = (int)$this->getTreeDigitsZip($zipTo);

        if (!isset($data['zipCodes'][$destinationZip])) {
            throw new \Exception('No such zip zone');
        }

        return $data['zipCodes'][$destinationZip];
    }

    /**
     * @param string $zipFrom
     *
     * @return array
     * @throws \Exception
     */
    protected function getDomesticZones(string $zipFrom): array
    {
        // prepare 3-digits ZIP Code
        $treeDigitsZip = $this->getTreeDigitsZip($zipFrom);

        // prepare file name
        $fileName = sprintf($this->cacheFile, $treeDigitsZip);

        // create file if not exists
        if (!file_exists($fileName)) {
            $data = $this->createDomesticZoneFile($treeDigitsZip, $fileName);
        } else {
            $data = json_decode(file_get_contents($fileName), true);
        }

        return $data;
    }

    /**
     * @param string $treeDigitsZip
     * @param string $fileName
     *
     * @return array
     * @throws \Exception
     */
    protected function createDomesticZoneFile(string $treeDigitsZip, string $fileName): array
    {
        $content = @file_get_contents(sprintf($this->domesticZoneApiMask, $treeDigitsZip, str_replace('_', '%2F', (new \DateTime())->format('m_d_Y'))));
        if (!empty($content)) {
            // parse content
            $content = json_decode($content, true);

            $i = 0;
            while (isset($content["Column$i"])) {
                foreach ($content["Column$i"] as $row) {
                    $parts = explode('---', $row['ZipCodes']);
                    if (isset($parts[1])) {
                        $from = (int)$parts[0];
                        $to = (int)$parts[1];
                        while ($from <= $to) {
                            $data['zipCodes'][$from] = (int)$row['Zone'];
                            $from++;
                        }
                    } else {
                        $data['zipCodes'][(int)$row['ZipCodes']] = (int)$row['Zone'];
                    }
                }
                $i++;
            }

            // create dir if not exist
            if (!file_exists('data')) {
                mkdir('data', 0777, true);
            }

            if (empty($data)) {
                throw new \Exception('API response parsing failed');
            } else {
                $data['createdAt'] = (new \DateTime())->format('Y-m-d H:i:s');
            }

            // create file
            file_put_contents($fileName, json_encode($data));
        } else {
            throw new \Exception('Domestic zone API unavailable');
        }

        return $data;
    }

    /**
     * @param string $zipCode
     *
     * @return string
     */
    protected function getTreeDigitsZip(string $zipCode): string
    {
        return mb_substr($zipCode, 0, 3);
    }
}
