<?php
/**
 * Created by PhpStorm.
 * User: itprofessor02
 * Date: 12.03.19
 * Time: 17:30
 */

use rollun\dic\InsideConstruct;
use rollun\logger\LifeCycleToken;
use service\Entity\Api\DataStore\Shipping\AllCosts;

error_reporting(E_ALL ^ E_USER_DEPRECATED ^ E_DEPRECATED); //E_ALL ^ E_USER_DEPRECATED

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/** @var \Interop\Container\ContainerInterface $container */
$container = require 'config/container.php';
InsideConstruct::setContainer($container);

$lifeCycleToken = LifeCycleToken::generateToken();
$container->setService(LifeCycleToken::class, $lifeCycleToken);

/** @var AllCosts $allCosts */
$allCosts = $container->get(AllCosts::class);

$content = [
    [
        'rollun_id', 'weight', 'height', 'width', 'depth', 'from', 'to', 'shipping_method_1', 'shipping_cost_1', 'shipping_method_2', 'shipping_cost_2', 'shipping_method_3', 'shipping_cost_3', 'shipping_method_4', 'shipping_cost_4'
    ]
];

$file = fopen('data/USPSprice.csv', 'r');
while (($line = fgetcsv($file, 99999, ";")) !== false) {
    if ($line[0] !== 'rollun_id') {
        $res = $allCosts->query($allCosts->buildUspShippingQuery($line[5], $line[6], (float)$line[1], (float)$line[3], (float)$line[4], (float)$line[2]));
        $row = $line;

        if (isset($res[0])) {
            $row[] = $res[0]['id'];
            $row[] = $res[0]['cost'];
        }
        if (isset($res[1])) {
            $row[] = $res[1]['id'];
            $row[] = $res[1]['cost'];
        }
        if (isset($res[2])) {
            $row[] = $res[2]['id'];
            $row[] = $res[2]['cost'];
        }
        if (isset($res[3])) {
            $row[] = $res[3]['id'];
            $row[] = $res[3]['cost'];
        }
        $content[] = $row;
    }
}
fclose($file);

// Open a file in write mode ('w')
$fp = fopen('data/USPSprice_processed.csv', 'w');

// Loop through file pointer and a line
foreach ($content as $fields) {
    fputcsv($fp, $fields, ';');
}

fclose($fp);

echo '<pre>';
print_r('123');
die();