<?php

namespace Evoweb\Extender\Tests\Functional12\Configuration;

use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Tests\Functional12\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends AbstractTestBase
{
    #[Test]
    public function hasBaseClassName(): void
    {
        $subject = new Register(['test' => []]);

        $condition = $subject->hasBaseClassName('test');

        self::assertTrue($condition);
    }

    #[Test]
    public function getExtendingClasses(): void
    {
        $expected = ['test2', 'test3'];

        $subject = new Register(['test' => $expected]);

        $actual = $subject->getExtendingClasses('test');

        self::assertEquals($expected, $actual);
    }
}
