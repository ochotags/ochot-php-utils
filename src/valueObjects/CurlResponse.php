<?php

declare(strict_types=1);

namespace app\shared\valueObjects;

class CurlResponse
{
    /** @var string */
    protected $response;
    /** @var int */
    protected $errno;
    /** @var string */
    protected $error;
    /** @var array<string> */
    protected $headers;

    //<editor-fold desc="Getters & Setters">
    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @param string $response
     */
    public function setResponse(string $response): void
    {
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function getErrno(): int
    {
        return $this->errno;
    }

    /**
     * @param int $errno
     */
    public function setErrno(int $errno): void
    {
        $this->errno = $errno;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string[] $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function addHeader(string $value, string $key = ''): void
    {
        if ($key != '') {
            $this->headers[$key] = $value;
        } else {
            $this->headers[] = $value;
        }
    }

    /**
     * @param string|int $key
     * @return string
     */
    public function getHeader($key): string
    {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        } else {
            return '';
        }
    }
    //</editor-fold>

    public function __construct(string $response = '', int $errno = 0, string $error = '', array $headers = [])
    {
        $this->response = $response;
        $this->errno    = $errno;
        $this->error   = $error;
        $this->headers = $headers;
    }
}
