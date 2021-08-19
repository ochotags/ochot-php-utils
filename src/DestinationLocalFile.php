<?php

declare(strict_types=1);

namespace OchoPhpUtils;

use OchoPhpUtils\interfaces\DestinationInterface;

class DestinationLocalFile implements DestinationInterface
{
    /** @var string */
    protected $filename;
    /** @var string */
    protected $folder;
    /** @var string */
    protected $finalPath;
    /** @var string */
    protected $dailyRotation;

    public function setDestination(string $filename = '', string $folder = '', bool $dailyRotation = true): void
    {
        $this->filename      = $filename;
        $this->folder        = $folder;
        $this->dailyRotation = ($dailyRotation) ? 'true' : 'false';

        $this->setFinalPath();
    }

    private function setFinalPath(): void
    {
        if ($this->filename == '' || $this->folder == '') {
            $this->finalPath = '';
            return;
        }
        // Down two levels until root folder:
        $folder = $this->folder;
        if (substr($folder, -1) != '/' && substr($folder, -1) != '\\') {
            $folder .= DIRECTORY_SEPARATOR;
        } else {
            $folder = substr($folder, 0, -1) . DIRECTORY_SEPARATOR;
        }

        $this->finalPath = $folder . $this->filename;
        if ($this->dailyRotation == 'true') {
            $this->finalPath .= '-' . date('Y-m-d');
        }
        $this->finalPath .= '.log';
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setValueParam(string $key, string $value): void
    {
        if ($key == 'filename') {
            $this->filename = $value;
        } elseif ($key == 'folder') {
            $this->folder = $value;
        } elseif ($key == 'dailyRotation') {
            $this->dailyRotation = $value;
        }

        $this->setFinalPath();
    }

    /**
     * @param string $key
     * @return string
     */
    public function getValueParam(string $key): string
    {
        if ($key == 'filename') {
            return $this->filename;
        } elseif ($key == 'folder') {
            return $this->folder;
        } elseif ($key == 'finalPath') {
            return $this->finalPath;
        } elseif ($key == 'dailyRotation') {
            return $this->dailyRotation;
        } else {
            return '';
        }
    }

    public function write(string $level, string $text): void
    {
        if ($this->finalPath != '') {
            $fhLog = fopen($this->finalPath, 'a+');
            $data  = '[' . date('Y-m-d h:i:s') . '] ';
            if ($level != '') {
                $data .= strtoupper($level) . ' - ';
            }
            $data .= $text;
            fwrite($fhLog, $data . PHP_EOL);
            fclose($fhLog);
        }
    }
}
