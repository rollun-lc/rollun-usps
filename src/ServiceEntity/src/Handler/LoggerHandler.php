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
use Jaeger\Thrift\Agent\AgentClient;
use Jaeger\Tracer\Tracer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;
use SplStack;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class LoggerHandler implements RequestHandlerInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoggerHandler constructor.
     * @param LoggerInterface|null $logger
     * @throws \ReflectionException
     */
    public function __construct(LoggerInterface $logger = null)
    {
        InsideConstruct::init(['logger' => LoggerInterface::class]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $level = $params['level'] ?? 'info';
        $message = $params['message'] ?? 'test messages';
        $context = $params['context'] ?? [];

        $data = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];

        if ($params['write_json'] ?? false) {
            $line = json_encode($context);
            fwrite(fopen('php://stdout', 'a'), $line);
        }

        if ($params['jaeger'] ?? false) {
            $transport = new \Jaeger\Transport\TUDPTransport('myjaeger-agent', '6832');
            $bufferTransport = new \Thrift\Transport\TBufferedTransport($transport);
            $binaryProtocol = new \Thrift\Protocol\TBinaryProtocol($bufferTransport);

            $client = new ThriftClient(
                'test-service-usps',
                new AgentClient($binaryProtocol)
            );

            $bufferTransport->open();

            $tracer = new Tracer(
                new SplStack(),
                new SpanFactory(
                    new RandomIntGenerator(),
                    new ConstSampler(true)
                ),
                $client
            );

            $span = $tracer->start('test ops', [
                new \Jaeger\Tag\StringTag('test.tag', 'qwe')
            ]);

            $span->addTag(new \Jaeger\Tag\BoolTag('test.bool.rest', true));

            $tracer->finish($span);

            $tracer->flush();

            $bufferTransport->close();
        }

        $this->logger->log($level, $message, $context);
        return new JsonResponse($data);
    }
}
