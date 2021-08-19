<?php

declare(strict_types=1);

namespace app\shared;

use app\shared\interfaces\DestinationInterface;
use app\shared\interfaces\LogsInterface;

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
