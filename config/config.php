<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/cache/config-cache.php',
];

// Determine application environment ('dev', 'test' or 'prod').
if (file_exists('.env')) {
    (new Dotenv())->load('.env');
}

// Determine application environment ('dev' or 'prod').
$appEnv = getenv('APP_ENV');

$aggregator = new ConfigAggregator([
    \rollun\repository\ConfigProvider::class,
    \rollun\utils\Metrics\ConfigProvider::class,
    \rollun\utils\FailedProcesses\ConfigProvider::class,
    \Zend\Cache\ConfigProvider::class,
    \Zend\Mail\ConfigProvider::class,
    \Zend\Db\ConfigProvider::class,
    \Zend\Validator\ConfigProvider::class,
    \Zend\HttpHandlerRunner\ConfigProvider::class,
    \Zend\Expressive\ConfigProvider::class,
    \Zend\Expressive\Router\ConfigProvider::class,
    // Include cache configuration
    new ArrayProvider($cacheConfig),
    // Rollun config
    \rollun\uploader\ConfigProvider::class,
    \rollun\callback\ConfigProvider::class,
    \rollun\datastore\ConfigProvider::class,
    \rollun\tracer\ConfigProvider::class,
    \rollun\logger\ConfigProvider::class,

    // Swoole config to overwrite some services (if installed)
    class_exists(\Zend\Expressive\Swoole\ConfigProvider::class) ? \Zend\Expressive\Swoole\ConfigProvider::class : function () {
        return [];
    },
    // Default App module config
    service\Entity\ConfigProvider::class,
    // Default App module config
    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
    // Load application config according to environment:
    //   - `global.dev.php`,   `global.test.php`,   `prod.global.prod.php`
    //   - `*.global.dev.php`, `*.global.test.php`, `*.prod.global.prod.php`
    //   - `local.dev.php`,    `local.test.php`,     `prod.local.prod.php`
    //   - `*.local.dev.php`,  `*.local.test.php`,  `*.prod.local.prod.php`
    new PhpFileProvider(realpath(__DIR__) . "/autoload/{{,*.}global.{$appEnv},{,*.}local.{$appEnv}}.php"),
    // Load development config if it exists
    new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
