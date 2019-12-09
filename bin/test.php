<?php
/**
 * Created by PhpStorm.
 * User: itprofessor02
 * Date: 12.03.19
 * Time: 17:30
 */

use Jaeger\Sampler\ConstSampler;
use Jaeger\Span\Factory\SpanFactory;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\dic\InsideConstruct;

use Jaeger\Client\ThriftClient;
use Jaeger\Id\RandomIntGenerator;
use Jaeger\Thrift\Agent\AgentClient;
use Jaeger\Tracer\Tracer;
use Jaeger\Transport\TUDPTransport;
use rollun\logger\LifeCycleToken;
use rollun\Service\Autobuy\DataStore\DropShip\EbayRollunDeals;
use rollun\utils\Json\Serializer;
use service\Shipping\Api\ShippingTypeResolver;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GeNode;
use Xiag\Rql\Parser\Query;
use Zend\ServiceManager\Factory\FactoryInterface;

error_reporting(E_ALL ^ E_USER_DEPRECATED ^ E_DEPRECATED); //E_ALL ^ E_USER_DEPRECATED

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/** @var \Interop\Container\ContainerInterface $container */
$container = require 'config/container.php';
InsideConstruct::setContainer($container);

$lifeCycleToken = LifeCycleToken::generateToken();
$container->setService(LifeCycleToken::class, $lifeCycleToken);

//EbayPlaisirDealsPickup
//EbayRollunDealsPickup

/** @var DataStoresInterface $megaDeals */
$megaDeals = $container->get(ShippingTypeResolver::class);
