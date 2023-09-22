<?php

namespace Evoweb\Extender\Tests\Functional\Configuration;

use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;

class RegisterTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function hasBaseClassName(): void
    {
        $subject = new Register(['test' => []]);

        $condition = $subject->hasBaseClassName('test');

        self::assertTrue($condition);
    }

    /**
     * @test
     */
    public function getExtendingClasses(): void
    {
        $expected = ['test2', 'test3'];

        $subject = new Register(['test' => $expected]);

        $actual = $subject->getExtendingClasses('test');

        self::assertEquals($expected, $actual);
    }
}
