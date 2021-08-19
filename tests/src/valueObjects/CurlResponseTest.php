<?php

declare(strict_types=1);

namespace tests\src\valueObjects;

use OchoPhpUtils\valueObjects\CurlResponse;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class CurlResponseTest extends TestCase
{
    /** @var string */
    private $response;
    /** @var int */
    private $errorno;
    /** @var string */
    private $error;
    /** @var array<string> */
    private $headers;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function iniVariables(): void
    {
        $this->response = 'tests';
        $this->errorno  = 5;
        $this->error   = 'error';
        $this->headers = ['test'];
    }

    private function commonAsserts(CurlResponse $object): void
    {
        $this->assertEquals(
            $this->response,
            $object->getResponse(),
            'reponse value is incorrect'
        );
        $this->assertEquals(
            $this->errorno,
            $object->getErrno(),
            'errorno value is incorrect'
        );
        $this->assertEquals(
            $this->error,
            $object->getError(),
            'error value is incorrect'
        );
        $this->assertEqualsCanonicalizing(
            $this->headers,
            $object->getHeaders(),
            'headers array is incorrect'
        );
    }

    /** @test */
    public function testConstructor(): void
    {
        $this->iniVariables();
        $object = new CurlResponse(
            $this->response,
            $this->errorno,
            $this->error,
            $this->headers
        );
        $this->commonAsserts($object);
    }

    /** @test */
    public function testSetters(): void
    {
        $this->iniVariables();
        $object = new CurlResponse();
        $object->setResponse($this->response);
        $object->setError($this->error);
        $object->setErrno($this->errorno);
        $object->setHeaders($this->headers);
        $this->commonAsserts($object);
    }

    /** @test */
    public function testAddHeader(): void
    {
        $this->iniVariables();
        $object = new CurlResponse(
            $this->response,
            $this->errorno,
            $this->error,
            $this->headers
        );
        $this->headers['new'] = 'data new';
        $object->addHeader('data new', 'new');
        $this->headers[] = 'data new 2';
        $object->addHeader('data new 2');
        $this->commonAsserts($object);
    }
}
