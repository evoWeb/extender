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

namespace Evoweb\Extender\Tests\Functional\Event;

use Evoweb\Extender\Event\RegisterAutoloaderEvent;
use Evoweb\Extender\Loader\ClassLoader;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RegisterAutoloaderEventTest extends AbstractTestBase
{
    #[Test]
    public function registerAutoloader(): void
    {
        new RegisterAutoloaderEvent(GeneralUtility::getContainer());

        $autoLoaders = spl_autoload_functions();
        $autoloader = reset($autoLoaders);

        $condition = is_array($autoloader) && $autoloader[0] instanceof ClassLoader;

        self::assertTrue($condition);
    }

    #[Test]
    public function autoloaderAlreadyRegistered(): void
    {
        $autoloaderClass = ClassLoader::class;
        $autoloader = [GeneralUtility::getContainer()->get($autoloaderClass), 'loadClass'];

        $subject = new class () extends RegisterAutoloaderEvent {
            public function __construct() {}

            /**
             * @param array<object|string> $autoloader
             */
            public function autoloaderAlreadyRegistered(array $autoloader): bool
            {
                return parent::autoloaderAlreadyRegistered($autoloader);
            }
        };

        $condition = $subject->autoloaderAlreadyRegistered($autoloader);

        self::assertTrue($condition);
    }

    #[Test]
    public function unregisterAutoloader(): void
    {
        $autoloaderClass = ClassLoader::class;
        $autoloader = [GeneralUtility::getContainer()->get($autoloaderClass), 'loadClass'];
        spl_autoload_register($autoloader, true, true);

        $subject = new class () extends RegisterAutoloaderEvent {
            public function __construct() {}

            /**
             * @param array<object|string> $autoloader
             */
            public function unregisterAutoloader(array $autoloader): void
            {
                parent::unregisterAutoloader($autoloader);
            }
        };

        $subject->unregisterAutoloader($autoloader);

        $condition = false;
        $currentAutoLoaders = spl_autoload_functions();
        foreach ($currentAutoLoaders as $currentAutoLoader) {
            if (
                is_array($currentAutoLoader)
                && get_class($currentAutoLoader[0]) === $autoloaderClass
            ) {
                $condition = true;
                break;
            }
        }

        self::assertTrue($condition);
    }

    #[Test]
    public function isPropagationStopped(): void
    {
        $registerAutoloaderEvent = new RegisterAutoloaderEvent(GeneralUtility::getContainer());

        // @extensionScannerIgnoreLine
        $condition = $registerAutoloaderEvent->isPropagationStopped();

        self::assertTrue($condition);
    }
}
