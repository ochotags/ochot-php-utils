<?php

declare(strict_types=1);

namespace OchoPhpUtils;

use OchoPhpUtils\interfaces\DestinationInterface;
use OchoPhpUtils\interfaces\LogsInterface;

class Logs implements LogsInterface
{
    /** @var DestinationInterface|null */
    private $destination;

    public function __construct(DestinationInterface $object = null)
    {
        $this->destination = $object;
    }

    /**
     * @param DestinationInterface $object
     */
    public function setDestination(DestinationInterface $object): void
    {
        $this->destination = $object;
    }


    public function writeLog(string $level, string $text): void
    {
        if ($this->destination != null) {
            $this->destination->write($level, $text);
        }
    }
}
