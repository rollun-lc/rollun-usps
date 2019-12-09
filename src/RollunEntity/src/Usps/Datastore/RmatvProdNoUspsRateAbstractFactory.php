<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\Entity\Usps\Datastore;

use Interop\Container\ContainerInterface;
use rollun\datastore\DataStore\DataStoreException;
use rollun\Usps\Datastore\RmatvProdNoUspsRate;
use rollun\datastore\DataStore\Factory\DataStoreAbstractFactory;

/**
 * Create and return an instance of the DataStore USPS price calculate be Prodno from RM Atv ftp Price
 * This Factory depends on Container (which should return an 'config' as array)
 *
 * The configuration can contain:
 * <code>
 *  'dataStore' => [
 *      'RmatvProdNoUspsRate' => [
 *          'class' => RmatvProdNoUspsRate::class,
 *          'rmFtpPriceDataStore' => 'rmFtpPrice', //service name for DataStore vith rmFtpPrice
 *      ]
 *  ]
 * </code>
 */
class RmatvProdNoUspsRateAbstractFactory extends DataStoreAbstractFactory
{

    const KEY_DS_RM_FTP_PRICE = 'rmFtpPriceDataStore';
    const KEY_ZIP_ORIGINATION = 'zipOrigination';
    const KEY_ZIP_DESTINATION = 'zipDestination';
    const DEFAULT_ZIP_ORIGINATION = 84651;
    const DEFAULT_ZIP_DESTINATION = 26505; //zone 7     //60619 zone 6

    public static $KEY_DATASTORE_CLASS = RmatvProdNoUspsRate::class;
    protected static $KEY_IN_CREATE = 0;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DS
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($this::$KEY_IN_CREATE) {
            throw new DataStoreException("Create will be called without pre call canCreate method");
        }

        $this::$KEY_IN_CREATE = 1;

        $config = $container->get('config');
        $serviceConfig = $config[self::KEY_DATASTORE][$requestedName];

        if (isset($serviceConfig[self::KEY_DS_RM_FTP_PRICE])) {
            if ($container->has($serviceConfig[self::KEY_DS_RM_FTP_PRICE])) {
                $requestedClassName = $container->get($serviceConfig[self::KEY_DS_RM_FTP_PRICE]);
            } else {
                $this::$KEY_IN_CREATE = 0;
                throw (
                new \RuntimeException('Can\'t create ' . $serviceConfig[self::KEY_DS_RM_FTP_PRICE])
                );
            }
        } else {
            $this::$KEY_IN_CREATE = 0;
            throw (
            new \InvalidArgumentException('There is not param  ' . self::KEY_DS_RM_FTP_PRICE . ' in config')
            );
        }

        $requestedClassName = $serviceConfig[self::KEY_CLASS];
        $rmFtpPriceDataStore = $container->get($serviceConfig[self::KEY_DS_RM_FTP_PRICE]);

        $zipOrigination = isset($serviceConfig[self::KEY_ZIP_ORIGINATION]) ?
                $serviceConfig[self::KEY_ZIP_ORIGINATION] : self::DEFAULT_ZIP_ORIGINATION;
        $zipDestination = isset($serviceConfig[self::KEY_ZIP_DESTINATION]) ?
                $serviceConfig[self::KEY_ZIP_DESTINATION] : self::DEFAULT_ZIP_DESTINATION;

        $this::$KEY_IN_CREATE = 0;
        return new $requestedClassName($rmFtpPriceDataStore, $zipOrigination, $zipDestination);
    }
}
