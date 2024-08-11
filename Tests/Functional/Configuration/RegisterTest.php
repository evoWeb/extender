<?php

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
