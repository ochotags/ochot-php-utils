<?php

declare(strict_types=1);

namespace tests\src;

use OchoPhpUtils\DestinationLocalFile;
use OchoPhpUtils\interfaces\DestinationInterface;
use OchoPhpUtils\Logs;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class LogsTest extends TestCase
{
    /** @var DestinationLocalFile */
    private $destination;
    /** @var Array<string> */
    private $level;
    /** @var Array<string> */
    private $message;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function generateRandomString($length = 10)
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function iniVariables(): void
    {
        $this->destination = new DestinationLocalFile();
        $this->destination->setDestination(
            'tests',
            dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'logs',
            false
        );

        $finalPathLogs = $this->destination->getValueParam('finalPath');

        if (file_exists($finalPathLogs)) {
            unlink($finalPathLogs);
        }

        $this->level[]   = 'error';
        $this->message[] = 'Error Message';
    }

    private function commonAsserts(DestinationInterface $destination): void
    {
        $logFile = $destination->getValueParam('finalPath');
        $exists  = file_exists($logFile);
        $this->assertEquals(true, $exists, 'logFile is not created correctly');

        $fhLog = fopen($logFile, 'r');
        $text  = fgets($fhLog);
        $idx   = 0;
        do {
            $this->assertStringContainsString($this->message[$idx], $text, 'message text is not created correctly');
            $this->assertStringContainsString(strtoupper($this->level[$idx]), $text, 'level is not created correctly');
            ++$idx;
        } while ($text = fgets($fhLog));

        $this->assertEquals(count($this->level), $idx, 'logFile no contains all lines');

        fclose($fhLog);
    }

    /** @test */
    public function testBasicWriteLog(): void
    {
        $this->iniVariables();
        $object = new Logs($this->destination);

        $object->writeLog($this->level[0], $this->message[0]);

        $this->commonAsserts($this->destination);
    }

    /** @test */
    public function testSetterObject(): void
    {
        $this->iniVariables();
        $object = new Logs();
        $object->setDestination($this->destination);

        $object->writeLog($this->level[0], $this->message[0]);

        $this->commonAsserts($this->destination);
    }

    /** @test */
    public function testMultilineLogFile(): void
    {
        $this->iniVariables();
        $object = new Logs();
        $object->setDestination($this->destination);

        for ($idx = 0; $idx < 9; ++$idx) {
            $this->level[]   = $this->generateRandomString(5);
            $this->message[] = $this->generateRandomString(25);
        }

        for ($idx = 0; $idx < 10; ++$idx) {
            $object->writeLog($this->level[$idx], $this->message[$idx]);
        }

        $this->commonAsserts($this->destination);
    }
}
