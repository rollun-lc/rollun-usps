<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace rollun\Entity\Usps;

use USPS\Rate;

class Rater extends Rate
{
//
//    /**
//     * Makes an HTTP request. This method can be overriden by subclasses if
//     * developers want to do fancier things or use something other than curl to
//     * make the request.
//     *
//     * @param resource $ch Optional initialized cURL handle
//     *
//     * @return string the response text
//     */
//    protected function doRequest($ch = null)
//    {
//        if (!$ch) {
//            $ch = curl_init();
//        }
//
//        $opts = self::$CURL_OPTS;
//        $opts[CURLOPT_POSTFIELDS] = http_build_query($this->getPostData(), null, '&');
//        $opts[CURLOPT_URL] = $this->getEndpoint();
//
//        // Replace 443 with 80 if it's not secured
//        if (strpos($opts[CURLOPT_URL], 'https://') === false) {
//            $opts[CURLOPT_PORT] = 80;
//        }
//
//        // set options
//        curl_setopt_array($ch, $opts);
//
//        // execute
//        $this->setResponse(curl_exec($ch));
//        $this->setHeaders(curl_getinfo($ch));
//
//        // fetch errors
//        $this->setErrorCode(curl_errno($ch));
//        $this->setErrorMessage(curl_error($ch));
//
//        // Convert response to array
//        $this->convertResponseToArray();
//
//        // If it failed then set error code and message
//        if ($this->isError()) {
//            $arrayResponse = $this->getArrayResponse();
//            $headers = $this->getHeaders();
//            $this->setErrorCode($headers['http_code']);
//            $this->setErrorMessage($arrayResponse);
//        }
//        // close
//        curl_close($ch);
//
//        return $this->getResponse();
//    }

    /**
     * Did we encounter an error?
     *
     * @return bool
     */
    public function isError()
    {
        $headers = $this->getHeaders();

        if ($headers['http_code'] != 200) {
            return true;
        }
        return false;
    }
}
