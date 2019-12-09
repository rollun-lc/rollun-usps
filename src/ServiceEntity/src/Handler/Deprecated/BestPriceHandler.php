<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Service\Usps\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use rollun\Usps\Package\Domestic\FirstClassCommercial\PackageService as PackageFirstClassCommercialPackageService;
use rollun\Usps\Package\Domestic\PriorityMailCommercial\PaddedEnvelope as PriorityMailCommercialPaddedEnvelope;
use rollun\Usps\ShippingPriceCommercial;
use rollun\Usps\ShippingData;
use Zend\Diactoros\Response\JsonResponse;
use rollun\Usps\ShippingDataManager;

/**
 * https://www.usps.com/business/web-tools-apis/rate-calculator-api.pdf
 */
class BestPriceHandler implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        //http://service-usps.loc/usps/best-price/?ZipOrigination=91601&ZipDestination=91730&Pounds=10&Ounces=10&Width=10&Length=10&Height=3

        $queryParams = $request->getQueryParams();
        $shippingData = new ShippingData($queryParams);

        $shippingDataManager = new ShippingDataManager($shippingData);
        $arrayOfShippingData = $shippingDataManager->getArrayOfShippingData();

        $shippingPrice = new ShippingPriceCommercial($shippingData);
        $responseData = $shippingPrice->getShippingPrice($arrayOfShippingData);

        $bestPrice = ["Price" => 1000000];
        foreach ($responseData as $value) {
            if (isset($value["Price"]) && $value["Price"] < $bestPrice["Price"]) {
                $bestPrice = $value;
            }
        }
        if ($bestPrice["Price"] == 1000000) {
            $bestPrice = [];
        }

        //$responseData = $shippingPrice->getPriorityAllPrices();
        //return new HtmlResponse("\n<pre style=\"font-size: 18px;\">\n" . print_r($bestPrice, true) . "\n</pre>\n");
        return new JsonResponse($responseData);
    }
}
