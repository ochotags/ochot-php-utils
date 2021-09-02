<?php

declare(strict_types=1);

namespace tests\src;

use OchoPhpUtils\DatetimeLibs;
use PHPUnit\Framework\TestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class DatetimeLibsTest extends TestCase
{
    /** @test */
    public function testSecondsToJiraTime(): void
    {
        $object = new DatetimeLibs();
        $this->assertEquals('58m', $object->secondsToJiraTime(3480), 'expected 58m');
        $this->assertEquals('', $object->secondsToJiraTime(-15), 'negative integer not working');
        $this->assertEquals('', $object->secondsToJiraTime(0), 'zero not working');
        $this->assertEquals('3h 41m', $object->secondsToJiraTime(13260), 'expected 3h 41m');
        $this->assertEquals('3h 41m 5s', $object->secondsToJiraTime(13265), 'expected 3h 41m 5s');
        $this->assertEquals('48h', $object->secondsToJiraTime(172800), 'expected 48h');
    }
}
