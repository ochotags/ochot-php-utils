<?php

declare(strict_types=1);

namespace OchoPhpUtils;

use OchoPhpUtils\valueObjects\CurlOptions;
use OchoPhpUtils\valueObjects\CurlResponse;

class CurlConnection
{
    /** @var CurlOptions */
    protected $extraOptions;

    //<editor-fold desc="Getters & Setters">

    /**
     * @return CurlOptions
     */
    public function getExtraOptions(): CurlOptions
    {
        return $this->extraOptions;
    }

    /**
     * @param CurlOptions $extraOptions
     */
    public function setExtraOptions(CurlOptions $extraOptions): void
    {
        $this->extraOptions = $extraOptions;
    }

    //</editor-fold>

    public function __construct(CurlOptions $extraOptions = null)
    {
        if ($extraOptions == null) {
            $this->extraOptions = new CurlOptions();
            $this->extraOptions->setMinimumOptions();
        } else {
            $this->extraOptions = $extraOptions;
        }
    }

    /**
     * @param array<string> $requestHeaders
     */
    public function call(
        string $url,
        string $method = 'GET',
        string $params = '',
        array $requestHeaders = []
    ): CurlResponse {
        $response = new CurlResponse();
        $curlCh   = curl_init($url);
        $this->curlSetopts($curlCh, $method, $params, $requestHeaders);
        $temp = curl_exec($curlCh);

        if ($temp === false) { // Deal with error communication: timeout or wrong URL
            $response->setResponse('');
            $response->setHeaders(['no_response' => true]);
            curl_close($curlCh);
            return $response;
        } else {
            $temp = (string)$temp;
            $response->setResponse($temp);
        }

        $response->setErrno(curl_errno($curlCh));
        $response->setError(curl_error($curlCh));

        // Retudn headers seperatly from the Response Body
        $header_size = (int)curl_getinfo($curlCh, CURLINFO_HEADER_SIZE);
        $headers     = substr($temp, 0, $header_size);
        $body        = substr($temp, $header_size);

        curl_close($curlCh);

        // Alternative system
//        [$headers, $bodytext] = preg_split("/\r\n\r\n|\n\n|\r\r/", (string)$temp, 2);
        $response->setHeaders($this->curlParseHeaders($headers));
        $response->setResponse($body);

        return $response;
    }

    /**
     * @param resource $curlCh
     * @param string $method
     * @param string $params
     * @param array<string> $requestHeaders
     */
    private function curlSetopts(
        $curlCh,
        string $method,
        string $params,
        array $requestHeaders
    ): void {
        $this->extraOptions->changeRequestMethod($method);
        $this->extraOptions->applyOptions2CurlHandler($curlCh);

        if (!empty($requestHeaders)) {
            curl_setopt($curlCh, CURLOPT_HTTPHEADER, $requestHeaders);
        }

        if ($method != 'GET' && $params != '') {
            curl_setopt($curlCh, CURLOPT_POSTFIELDS, $params);
        }
    }

    /**
     * @param string $messageHeaders
     * @return array<string>
     */
    private function curlParseHeaders(string $messageHeaders): array
    {
        $headerLines = preg_split("/\r\n|\n|\r/", $messageHeaders);
        $headers     = [];
        $tmp         = explode(' ', trim(array_shift($headerLines)), 3);
        if (isset($tmp[1])) {
            $headers['http_status_code'] = $tmp[1];
        }
        if (isset($tmp[2])) {
            $headers['http_status_message'] = $tmp[2];
        }
        foreach ($headerLines as $line) {
            $temp = explode(':', $line, 2);

            if (count($temp) == 2) {
                $name           = strtolower($temp[0]);
                $headers[$name] = trim($temp[1]);
            } else {
                $headers[] = trim($temp[0]);
            }
        }

        return $headers;
    }
}
