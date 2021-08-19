<?php

declare(strict_types=1);

namespace OchoPhpUtils\valueObjects;

class CurlOptions
{
    /** @var array<string|int|bool|array> */
    protected $options;

    //<editor-fold desc="Getters & Setters">
    //</editor-fold>

    public function clearOptions()
    {
        $this->options = [];
        $this->setMinimumOptions();
    }

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->setMinimumOptions();
    }

    public function addCustomHttpHeader(string $header)
    {
        if (!isset($this->options['CURLOPT_HTTPHEADER'])) {
            $this->options['CURLOPT_HTTPHEADER'] = [];
        }
        $this->options['CURLOPT_HTTPHEADER'][] = $header;
    }

    public function setMinimumOptions()
    {
        $this->options['CURLOPT_HEADER']         = true;
        $this->options['CURLOPT_RETURNTRANSFER'] = true;
        $this->options['CURLOPT_FOLLOWLOCATION'] = true;
//        $this->options['CURLOPT_VERBOSE']        = true;
//        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $this->options['CURLOPT_TIMEOUT']       = 90;
        $this->options['CURLOPT_CUSTOMREQUEST'] = 'GET';
    }

    public function setStandardShopify()
    {
        $this->setMinimumOptions();
        $this->options['CURLOPT_MAXREDIRS']      = 3;
        $this->options['CURLOPT_SSL_VERIFYPEER'] = true;
        $this->options['CURLOPT_SSL_VERIFYHOST'] = 2;
        $this->options['CURLOPT_USERAGENT']      = 'ohShopify-php-api-client';
        $this->options['CURLOPT_CONNECTTIMEOUT'] = 30;
        $this->options['CURLOPT_TIMEOUT']        = 30;
        $this->options['CURLOPT_CUSTOMREQUEST']  = 'GET';
        $this->options['CURLOPT_HTTPHEADER']     = ['Content-Type: application/json'];
    }

    public function setAntiCloudflareProtection()
    {
        $this->setMinimumOptions();
        $this->options['CURLOPT_HEADER']     = 0;
        $this->options['CURLOPT_POST']       = true;
        $this->options['CURLOPT_HTTPHEADER'] = ['Content-Length: 0'];
    }

    public function addIgnoreSslErrors()
    {
        $this->options['CURLOPT_SSL_VERIFYPEER'] = false;
        $this->options['CURLOPT_SSL_VERIFYHOST'] = 0;
    }

    public function changeRequestMethod(string $value)
    {
        $this->options['CURLOPT_CUSTOMREQUEST'] = $value;
    }

    public function changeTimeout(int $seconds)
    {
        $this->options['CURLOPT_CONNECTTIMEOUT'] = $seconds;
        $this->options['CURLOPT_TIMEOUT']        = $seconds;
    }

    public function addUserPassword(string $user, string $password)
    {
        $this->options['CURLOPT_USERPWD'] = $user . ':' . $password;
    }

    public function changeUserAgent(string $userAgent)
    {
        $this->options['CURLOPT_USERAGENT'] = $userAgent;
    }

    public function addUserAgentGoogleBot()
    {
        $this->changeUserAgent('Googlebot/2.1 (+http://www.google.com/bot.html)');
    }

    public function setStandardCinemas(string $hostname = '', bool $gzip = false)
    {
        // Options
        $this->setMinimumOptions();
        $this->addUserAgentGoogleBot();
        $this->addIgnoreSslErrors();
        if ($gzip) {
            $this->addGzip();
        }

        // Headers
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3',
            'Cookie: __utma=70771970.620572690.1532629814.1532629814.1533689086.2; __utmz=70771970.1532629814.1.1.' .
            'utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __HFCTL=0; __HFUID=42c84ec743e084f3ab9d2f3ee510c4aa; ' .
            '__utmb=70771970.7.10.1533689086; __utmc=70771970; __utmt=1;' . ($hostname != '') ? $hostname . ';' : '',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1'
        ];
        if ($hostname != '') {
            $headers[] = 'Host: ' . $hostname;
        }
        $this->options['CURLOPT_HTTPHEADER'] = $headers;
    }

    public function addGzip()
    {
        $this->options['CURLOPT_ENCODING'] = 'gzip,deflate';
    }

    public function applyOptions2CurlHandler($curlHandler)
    {
        foreach ($this->options as $optName => $value) {
            curl_setopt($curlHandler, constant($optName), $value);
        }
    }
}
