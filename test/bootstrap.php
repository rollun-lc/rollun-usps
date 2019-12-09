<?php

use rollun\logger\LifeCycleToken;
use Symfony\Component\Dotenv\Dotenv;

global $argv;

error_reporting(E_ALL ^ E_DEPRECATED ^ E_USER_DEPRECATED);

// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));

// Determine application environment ('dev', 'test' or 'prod').
if (file_exists('.env')) {
    (new Dotenv())->load('.env');
}

$appEnv = getenv("APP_ENV");
if ($appEnv == 'prod') {
    echo "You cannot start test if prod environment. Var APP_ENV must set in dev or test!";
    exit(1);
}

// Setup autoloading
require 'vendor/autoload.php';


$container = require 'config/container.php';
$lifeCycleToken = LifeCycleToken::generateToken();
$container->setService(LifeCycleToken::class, $lifeCycleToken);
\rollun\dic\InsideConstruct::setContainer($container);
