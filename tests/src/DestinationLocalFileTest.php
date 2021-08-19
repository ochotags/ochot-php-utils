<?php

declare(strict_types=1);

namespace tests\src;

use OchoPhpUtils\DestinationLocalFile;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class DestinationLocalFileTest extends TestCase
{
    /** @var string */
    private $filename;
    /** @var string */
    private $folder;
    /** @var string */
    private $dailyRotation;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function iniVariables(): void
    {
        $this->filename      = 'tests';
        $this->folder        = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'logs';
        $this->dailyRotation = 'false';
    }

    private function calculateLogPath(): string
    {
        $dsp = DIRECTORY_SEPARATOR;
        // We are inside tests folders, then we must remove it.
//        $rootFolder        = str_replace('tests\\', '', dirname(__FILE__));
        $expectedFinalPath = $this->folder . $dsp . $this->filename;
        if ($this->dailyRotation == 'true') {
            $expectedFinalPath .= '-' . date('Y-m-d');
        }
        $expectedFinalPath .= '.log';
        return $expectedFinalPath;
    }

    private function commonAsserts(DestinationLocalFile $object): void
    {
        $this->assertEquals(
            $this->filename,
            $object->getValueParam('filename'),
            'filename value is incorrect'
        );
        $this->assertEquals(
            $this->folder,
            $object->getValueParam('folder'),
            'folder value is incorrect'
        );
        $this->assertEquals(
            $this->dailyRotation,
            $object->getValueParam('dailyRotation'),
            'dailyRotation value is incorrect'
        );
        $expectedFinalPath = $this->calculateLogPath();
        $this->assertEquals(
            $expectedFinalPath,
            $object->getValueParam('finalPath'),
            'finalPath value is incorrect'
        );
    }

    /** @test */
    public function testBasicInitializeObject(): void
    {
        $this->iniVariables();
        $object = new DestinationLocalFile();

        $object->setDestination(
            $this->filename,
            $this->folder,
            $this->dailyRotation == 'true'
        );

        $this->commonAsserts($object);
    }

    /** @test */
    public function testBasicSettersGetters(): void
    {
        $this->iniVariables();
        $object = new DestinationLocalFile();

        $object->setValueParam('filename', $this->filename);
        $object->setValueParam('folder', $this->folder);
        $object->setValueParam('dailyRotation', $this->dailyRotation);

        $this->assertEquals(
            $this->dailyRotation,
            $object->getValueParam('dailyRotation'),
            'dailyRotation value is incorrect'
        );
        $this->assertEquals(
            $this->folder,
            $object->getValueParam('folder'),
            'folder value is incorrect'
        );
        $this->assertEquals(
            $this->filename,
            $object->getValueParam('filename'),
            'filename value is incorrect'
        );
        $this->assertEquals(
            '',
            $object->getValueParam('other'),
            'other value is incorrect'
        );
    }

    /** @test */
    public function testDailyRotationInitializeObject(): void
    {
        $this->iniVariables();
        $object              = new DestinationLocalFile();
        $this->dailyRotation = 'true';
        $object->setDestination($this->filename, $this->folder, $this->dailyRotation == 'true');
        $this->commonAsserts($object);
    }

    /** @test */
    public function testSetFilenameManuallyObject(): void
    {
        $this->iniVariables();
        $object = new DestinationLocalFile();

        $object->setDestination($this->filename, $this->folder, $this->dailyRotation == 'true');

        $this->filename = 'foo';
        $object->setValueParam('filename', $this->filename);
        $this->commonAsserts($object);

        $this->filename = 'bar';
        $object->setValueParam('filename', $this->filename);
        $this->commonAsserts($object);
    }

    /** @test */
    public function testSetFolderManuallyObject(): void
    {
        $this->iniVariables();
        $object = new DestinationLocalFile();

        $object->setDestination($this->filename, $this->folder, $this->dailyRotation == 'true');

        $this->folder = 'foo';
        $object->setValueParam('folder', $this->folder);
        $this->commonAsserts($object);

        $this->folder = 'bar';
        $object->setValueParam('folder', $this->folder);
        $this->commonAsserts($object);
    }

    /** @test */
    public function testSetDailyRotationManuallyObject(): void
    {
        $this->iniVariables();
        $object = new DestinationLocalFile();
        $object->setDestination($this->filename, $this->folder, $this->dailyRotation == 'true');

        $this->dailyRotation = 'true';
        $object->setValueParam('dailyRotation', $this->dailyRotation);
        $this->commonAsserts($object);
    }

    /** @test */
    public function testFolderWithSlash(): void
    {
        $this->iniVariables();

        $expectedFinalPath = str_replace('/\\', DIRECTORY_SEPARATOR, $this->calculateLogPath());

        $this->folder = $this->folder . '/';

        $object = new DestinationLocalFile();
        $object->setDestination($this->filename, $this->folder, $this->dailyRotation == 'true');

        $this->assertEquals(
            $this->folder,
            $object->getValueParam('folder'),
            'folder value is incorrect'
        );
        $this->assertEquals(
            $expectedFinalPath,
            $object->getValueParam('finalPath'),
            'finalPath value is incorrect'
        );

        $this->iniVariables();
        $this->folder = $this->folder . '\\';

        $object = new DestinationLocalFile();
        $object->setDestination($this->filename, $this->folder, $this->dailyRotation == 'true');

        $this->assertEquals(
            $this->folder,
            $object->getValueParam('folder'),
            'folder value is incorrect'
        );
        $this->assertEquals(
            $expectedFinalPath,
            $object->getValueParam('finalPath'),
            'finalPath value is incorrect'
        );
    }

    /** @test */
    public function testWriteLog(): void
    {
        $this->iniVariables();
        $object = new DestinationLocalFile();
        $object->setDestination($this->filename, $this->folder, $this->dailyRotation == 'true');
        $finalPath = $this->calculateLogPath();

        if (file_exists($finalPath)) {
            unlink($finalPath);
        }

        $object->write('level', 'message');

        $exists = file_exists($finalPath);
        $this->assertEquals(true, $exists, 'finalPath is not created correctly');

        $fhLog = fopen($finalPath, 'r');
        $text  = fgets($fhLog);
        $this->assertStringContainsString('message', $text, 'message text is not created correctly');
        $this->assertStringContainsString(strtoupper('level'), $text, 'level is not created correctly');
        fclose($fhLog);
        unlink($finalPath);
    }
}
