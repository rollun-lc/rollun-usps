<?php

/**
 * Created by PhpStorm.
 * User: victor
 * Date: 27.01.19
 * Time: 12:53
 */

namespace service\Entity\Handler\Shipping;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;
use Zend\Diactoros\Response\HtmlResponse;
use rollun\datastore\Rql\RqlParser;
use service\Entity\Api\DataStore\Shipping\AllCosts;
use rollun\Entity\Subject\Address;
use rollun\Entity\Product\Dimensions\Rectangular;
use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\ShippingRequest;

class BestShippingHandler implements RequestHandlerInterface
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
        InsideConstruct::init([
            'logger' => LoggerInterface::class
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryString = $request->getServerParams()['QUERY_STRING'];
        if (empty($queryString)) {
            $message = "\n<pre style=\"font-size: 18px;\">\n"
                    . "Try: \n"
                    . 'http://service-usps.loc/shipping/best-price/?'
                    . 'ZipOrigination=91601&ZipDestination=91730&Width=10&Length=10&Height=2&Pounds=0.5'
                    . '&like(id,*Usps-PM*)&sort(cost)&ne(cost,null())'
                    . "\n</pre>\n";
            $response = new HtmlResponse($message);
            return $response;
        }

        $strPos = strpos($queryString, '&XDEBUG_SESSION');
        if ($strPos !== false) {
            $queryString = substr($queryString, 0, $strPos);
        }

        $query = RqlParser::rqlDecode($queryString);
        $allCostsDataStore = new AllCosts();

        $message = "\n<pre style=\"font-size: 18px;\">\n"
                . print_r($allCostsDataStore->query($query), true)
                . "\n</pre>\n";
        return new HtmlResponse($message);
    }
}
