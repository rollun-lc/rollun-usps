<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Service\Usps\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use rollun\Usps\Package\Domestic\FirstClassCommercial\PackageService as PackageFirstClassCommercialPackageService;
use rollun\Usps\Package\Domestic\PriorityMailCommercial\PaddedEnvelope as PriorityMailCommercialPaddedEnvelope;
use rollun\Usps\ShippingPriceCommercial;
use rollun\Usps\ShippingData;
use rollun\Usps\ShippingDataManager;
use Zend\Diactoros\Response\JsonResponse;

/**
 * https://www.usps.com/business/web-tools-apis/rate-calculator-api.pdf
 */
class AllPriceHandler implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        //http://service-usps.loc/usps/all-price/?ZipOrigination=91601&ZipDestination=91730&Pounds=10&Ounces=10&Width=10&Length=10&Height=3&Service=PRIORITY%20COMMERCIAL&Container=REGIONAL%20RATE%20BOX%20B
        //http://service-usps.loc/?ZipOrigination=91601&ZipDestination=91730&Pounds=10&Ounces=10&Width=10&Length=10&Height=3

        $queryParams = $request->getQueryParams();
        $shippingData = new ShippingData($queryParams);

        $shippingDataManager = new ShippingDataManager($shippingData);
        $arrayOfShippingData = $shippingDataManager->getArrayOfShippingData();

        $shippingPrice = new ShippingPriceCommercial($shippingData);
        $responseData = $shippingPrice->getShippingPrice($arrayOfShippingData);

        //$responseData = $shippingPrice->getPriorityAllPrices();
        //return new HtmlResponse("\n<pre style=\"font-size: 18px;\">\n" . print_r($responseData, true) . "\n</pre>\n");
        return new JsonResponse($responseData);
    }
}
