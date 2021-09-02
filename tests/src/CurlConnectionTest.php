<?php

declare(strict_types=1);

namespace tests\src;

use OchoPhpUtils\CurlConnection;
use OchoPhpUtils\valueObjects\CurlOptions;
use OchoPhpUtils\valueObjects\CurlResponse;
use PHPUnit\Framework\TestCase;

//use tests\helper\ArrayTestCaseTrait;

/** @psalm-suppress PropertyNotSetInConstructor */
class CurlConnectionTest extends TestCase
{
//    use ArrayTestCaseTrait;

    /** @var string */
    private $response;
    /** @var int */
    private $errorno;
    /** @var string */
    private $error;
    /** @var string */
    private $headerKey;
    /** @var int|string */
    private $headerValue;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function iniVariables(): void
    {
        $this->response    = '<meta content="Google.es';
        $this->errorno     = 0;
        $this->error       = '';
        $this->headerKey   = 'http_status_code';
        $this->headerValue = 200;
    }

    private function commonAsserts(CurlResponse $object): void
    {
        $this->assertTrue(
            strpos($object->getResponse(), $this->response) > 0,
            'response value is incorrect'
        );
        $this->assertEquals(
            $this->errorno,
            $object->getErrno(),
            'error number value is incorrect'
        );
        $this->assertEquals(
            $this->error,
            $object->getError(),
            'error value is incorrect'
        );
        $aux = $object->getHeaders();
        if ($this->headerKey != '') {
            $this->assertArrayHasKey(
                'http_status_code',
                $aux,
                'the array has the http_status_code key'
            );
        }
        if ($this->headerValue != '') {
            $this->assertEquals(
                $this->headerValue,
                $aux[$this->headerKey],
                'http_status_code value is incorrect'
            );
        }
    }

    /** @test */
    public function testBasicObject(): void
    {
        $this->iniVariables();
        $object = new CurlConnection();

        $response = $object->call('https://www.google.es', 'GET');

        $this->commonAsserts($response);
    }

    /** @test */
    public function testPostCallObject(): void
    {
        $this->iniVariables();
        $object = new CurlConnection();

        $response = $object->call('https://jsonplaceholder.typicode.com/posts', 'POST');

        $this->response    = '"id": ';
        $this->headerValue = 201;

        $this->commonAsserts($response);
    }

    /** @test */
    public function testStandardShopify(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->setStandardShopify();
        $object = new CurlConnection($options);

        $api_key             = '39f3892ef6996aa9f9d78b3b361ef693';
        $api_secret          = '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';
        $api_url             = 'oasisfloral-es.myshopify.com';
        $api_version         = '2021-04';
        $url_get_collections = 'https://' . $api_key . ':' . $api_secret . '@' . $api_url . '/admin/api/' .
            $api_version . '/custom_collections.json';

        $response = $object->call($url_get_collections, 'GET');

        $this->assertTrue(
            strpos($response->getResponse(), '"custom_collections"') > 0,
            'response value is incorrect'
        );

        $this->assertEquals(
            $api_version,
            $response->getHeader('x-shopify-api-version'),
            'header "x-shopify-api-version" value is incorrect'
        );

        $this->assertEquals(
            '',
            $response->getHeader('unkown-header'),
            'header "unkown-header" value is incorrect'
        );
    }

    /** @test */
    public function testStandardCinemas(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->setStandardCinemas('cine.entradas.com');
        $object = new CurlConnection($options);

        $url = 'https://cine.entradas.com/ajax/getShowsForCinemas?cinemaIds[]=3015';

        $response = $object->call($url);

        $this->response    = '"shows"';
        $this->headerValue = 200;

        $this->commonAsserts($response);
    }

    /** @test */
    public function testStandardCinemasGzip(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->setStandardCinemas('www.compraentradas.com', true);
        $object = new CurlConnection($options);

        $url = 'https://www.compraentradas.com/Cine/61/cineapolis-el-teler';

        $response = $object->call($url);

        $this->response    = '<title>Cartelera Cineapolis El Teler - CompraEntradas.com - ' .
            'El mejor cine al mejor precio</title>';
        $this->headerValue = 200;

        $this->commonAsserts($response);
    }

    /** @test */
    public function testAntiCloudflareProtection(): void
    {
        $this->iniVariables();

        /* OK TEST */
        $options = new CurlOptions();
        $options->setAntiCloudflareProtection();
        $object = new CurlConnection($options);

        $url = 'https://www.cinesa.es/Cines/Horarios/280/29600';

        $response          = $object->call($url);
        $this->response    = '"peliculas"';
        $tomorrow          = strtotime('+1 day');
        $this->headerValue = date('d', $tomorrow) . '/09","peliculas":[{"idgrupo":12410,"titulo":"After.';
        $this->commonAsserts($response);

        /* KO TEST */
        $options->clearOptions();
        $options->setStandardCinemas('www.cinesa.es');
        $object->setExtraOptions($options);

        $response          = $object->call($url);
        $this->response    = 'Attention Required! | Cloudflare';
        $this->headerValue = 403;
        $this->commonAsserts($response);
    }

    /** @test */
    public function testSslAddCustomHeader(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->addCustomHttpHeader('Content-Type: text/xml');
        $options->addIgnoreSslErrors();
        $object = new CurlConnection($options);

        $url      = 'https://oasistest.smithersoasisws.com:7443/PY920/CustomInventoryManager';
        $response = $object->call($url, 'POST');

        $this->response    = 'S:Envelope';
        $this->headerValue = 500;

        $this->commonAsserts($response);
    }

    /** @test */
    public function testCustomHeader(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->addIgnoreSslErrors();
        $object = new CurlConnection($options);

        $url      = 'https://oasistest.smithersoasisws.com:7443/PY920/CustomInventoryManager';
        $response = $object->call($url, 'POST', '', ['Content-Type: text/xml']);

        $temp = $object->getExtraOptions();
        $temp->setStandardShopify();

        $this->response    = 'S:Envelope';
        $this->headerValue = 500;

        $this->commonAsserts($response);
    }

    /** @test */
    public function testChangeTimeout(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->changeTimeout(1);
        $object = new CurlConnection($options);

        $url      = 'https://reqres.in/api/users?delay=3';
        $response = $object->call($url);

        $this->assertEquals(
            '',
            $response->getResponse(),
            'response value is incorrect'
        );
        $aux = $response->getHeaders();
        $this->assertArrayHasKey(
            'no_response',
            $aux,
            'the array has the no_response key'
        );
        $this->assertEquals(
            true,
            $aux['no_response'],
            'no_response value is incorrect'
        );
    }

    /** @test */
    public function testPostParams(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->setStandardShopify();
        $object = new CurlConnection($options);

        $inventoryId = 42519226417320;
        $url = 'https://' . '39f3892ef6996aa9f9d78b3b361ef693' . ':' .
            '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' . '@' . 'oasisfloral-es.myshopify.com' .
            '/admin/api/' . '2021-07' . '/inventory_levels.json?inventory_item_ids=' . $inventoryId;

        $response = $object->call($url);
        $response = json_decode($response->getResponse());
        $currentQty = $response->inventory_levels[0]->available;
        $locationId = $response->inventory_levels[0]->location_id;

        $url      = 'https://' . '39f3892ef6996aa9f9d78b3b361ef693' . ':' .
            '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' . '@' . 'oasisfloral-es.myshopify.com' .
            '/admin/api/' . '2021-07' . '/inventory_levels/set.json';
        $postParams = [
            "location_id" => $locationId,
            "inventory_item_id" => $inventoryId,
            "available" => $currentQty
        ];
        $response = $object->call($url, 'POST', json_encode($postParams, JSON_INVALID_UTF8_SUBSTITUTE));

        $this->response    = '"inventory_level":{"inventory_item_id"';
        $this->headerValue = 200;

        $this->commonAsserts($response);
    }

    /** @test */
    public function testUserPassword(): void
    {
        $this->iniVariables();
        $options = new CurlOptions();
        $options->addUserPassword('hola', 'hola');
    }
}
