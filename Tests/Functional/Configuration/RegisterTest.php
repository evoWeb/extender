<?php

namespace Evoweb\Extender\Tests\Functional\Configuration;

use Evoweb\Extender\Configuration\ClassRegister;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends AbstractTestBase
{
    #[Test]
    public function hasBaseClassName(): void
    {
        $subject = new ClassRegister(['test' => []]);

        $condition = $subject->hasBaseClassName('test');

        self::assertTrue($condition);
    }

    #[Test]
    public function getExtendingClasses(): void
    {
        $expected = ['test2', 'test3'];

        $subject = new ClassRegister(['test' => $expected]);

        $actual = $subject->getExtendingClasses('test');

        self::assertEquals($expected, $actual);
    }
}
