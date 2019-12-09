<?php
/**
 * Created by PhpStorm.
 * User: itprofessor02
 * Date: 19.03.19
 * Time: 15:56
 */

return [
    \Jaeger\Tracer\Tracer::class => [
        'host' => 'myjaeger-agent',
        'serviceName' => getenv('SERVICE_NAME') !== false ? getenv('SERVICE_NAME') : 'usps-service',
        'debugEnable' => getenv('APP_DEBUG') !== false ? getenv('APP_DEBUG') : true,
    ]
];