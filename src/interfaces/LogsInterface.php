<?php

declare(strict_types=1);

namespace OchoPhpUtils\interfaces;

interface LogsInterface
{
    /**
     * @param DestinationInterface $object
     */
    public function setDestination(DestinationInterface $object): void;

    public function writeLog(string $level, string $text): void;
}
