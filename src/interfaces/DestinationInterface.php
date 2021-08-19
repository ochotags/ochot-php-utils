<?php

declare(strict_types=1);

namespace OchoPhpUtils\interfaces;

interface DestinationInterface
{
    /**
     * @param string $key
     * @param string $value
     */
    public function setValueParam(string $key, string $value): void;

    /**
     * @param string $key
     * @return string
     */
    public function getValueParam(string $key): string;

    public function write(string $level, string $text): void;
}
