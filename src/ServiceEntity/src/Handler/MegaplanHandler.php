<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 27.01.19
 * Time: 12:53
 */

namespace service\Entity\Handler;


use Jaeger\Client\ThriftClient;
use Jaeger\Id\RandomIntGenerator;
use Jaeger\Sampler\ConstSampler;
use Jaeger\Span\Factory\SpanFactory;
use Jaeger\Tag\StringTag;
use Jaeger\Thrift\Agent\AgentClient;
use Jaeger\Thrift\Log;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;
use SplStack;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class MegaplanHandler implements RequestHandlerInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * LoggerHandler constructor.
     * @param LoggerInterface|null $logger
     * @param Tracer|null $tracer
     * @throws \ReflectionException
     */
    public function __construct(LoggerInterface $logger = null, Tracer $tracer = null)
    {
        InsideConstruct::init(['logger' => LoggerInterface::class, 'tracer' => Tracer::class]);
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * @throws \ReflectionException
     */
    public function __wakeup()
    {
        InsideConstruct::initWakeup(['logger' => LoggerInterface::class, 'tracer' => Tracer::class]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $span = $this->tracer->start('MegaplanHandler.handle');

        $params = $request->getQueryParams();
        $body = $request->getBody()->__toString();

        $span->addTag(new StringTag('request.uri', $request->getUri()->__toString()));

        foreach ($params as $paramName => $paramValue) {
            if (is_array($paramValue)) {
                foreach ($paramValue as $paramValueKey => $paramValueVal) {
                    $span->addTag(new StringTag("request.get.$paramName.$paramValueKey", $paramValueVal));
                }
            } else {
                $span->addTag(new StringTag("request.get.$paramName", $paramValue));
            }
        }
        $span->addTag(new StringTag('request.body', $body));
        foreach ($request->getHeaders() as $headerName => $header) {
            $span->addTag(new StringTag("request.header.$headerName", implode(', ', $header)));
        }

        $this->logger->debug('Recieved megaplan request', [
            'get' => $params,
            'body' => $body,
        ]);


        $this->tracer->finish($span);
        $this->tracer->flush();
        $data = json_decode($body);
        return new JsonResponse([
            'Id' => $data->data->deal->Id ?? 0,
            'Model' => [
                'Category1000047CustomFieldUspsshipping' => '3.84',
                'Category1000047CustomFieldRmshipping' => '6.66',
                'Category1000047CustomFieldPickup' => 'Да',
            ]
        ]);
    }
}
