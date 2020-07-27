<?php
/**
 * Created by PhpStorm.
 * User: itprofessor02
 * Date: 12.03.19
 * Time: 17:30
 */

use Interop\Container\ContainerInterface;
use rollun\dic\InsideConstruct;
use rollun\logger\LifeCycleToken;
use service\Entity\Api\DataStore\Shipping\AllCosts;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Query;

error_reporting(E_ALL ^ E_USER_DEPRECATED ^ E_DEPRECATED); //E_ALL ^ E_USER_DEPRECATED

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/** @var ContainerInterface $container */
$container = require 'config/container.php';

InsideConstruct::setContainer($container);

$container->setService(LifeCycleToken::class, LifeCycleToken::generateToken());

/** @var AllCosts $allCosts */
$allCosts = $container->get(AllCosts::class);

$query = new Query();
$andNode = new AndNode(
    [
        new EqNode('ZipOrigination', '84663'),
        new EqNode('ZipDestination', '78228'),
        new EqNode('Pounds', 0.4),
        new EqNode('Width', 1),
        new EqNode('Length', 2),
        new EqNode('Height', 1),
        new EqNode('Error', null),
        new NeNode('cost', null),
        new EqNode('Quantity', 1),
        new EqNode('attr_dangerous', true),
        new EqNode('attr_tire', true),
    ]
);

$query->setQuery($andNode);
$query->setSort(new SortNode(['cost' => SortNode::SORT_ASC]));

$result = $allCosts->query($query);

echo '<pre>';
print_r($result);
die();