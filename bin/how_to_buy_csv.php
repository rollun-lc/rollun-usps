<?php

use rollun\dic\InsideConstruct;
use rollun\logger\LifeCycleToken;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;
use service\Entity\Api\DataStore\Shipping\BestShipping;

error_reporting(E_ALL ^ E_USER_DEPRECATED ^ E_DEPRECATED); //E_ALL ^ E_USER_DEPRECATED

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/** @var \Interop\Container\ContainerInterface $container */
$container = require 'config/container.php';
InsideConstruct::setContainer($container);

$lifeCycleToken = LifeCycleToken::generateToken();
$container->setService(LifeCycleToken::class, $lifeCycleToken);

/** @var BestShipping $bestShipping */
$bestShipping = $container->get(BestShipping::class);

$content = [
    ['RollunId', 'ZipDestination', 'ShippingMethod']
];

$file = fopen('data/how_to_buy.csv', 'r');
while (($line = fgetcsv($file, 99999, ",")) !== false) {
    $res = $bestShipping->query(buildQuery($line[0], $line[1]));

    $row = $line;
    if (isset($res['id'])) {
        $row[] = $res['id'];
    }

    $content[] = $row;
}
fclose($file);

// Open a file in write mode ('w')
$fp = fopen('data/how_to_buy(processed).csv', 'w');

// Loop through file pointer and a line
foreach ($content as $fields) {
    fputcsv($fp, $fields, ';');
}

fclose($fp);

echo '<pre>';
print_r('Done!');
die();

/**
 * @param string $rollunId
 * @param string $zipDestination
 *
 * @return Query
 */
function buildQuery(string $rollunId, string $zipDestination): Query
{
    $query = new Query();
    $andNode = new AndNode(
        [
            new EqNode('RollunId', $rollunId),
            new EqNode('ZipDestination', $zipDestination)
        ]
    );

    $query->setQuery($andNode);

    return $query;
}