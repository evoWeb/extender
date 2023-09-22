<?php

namespace Evoweb\Extender\Tests\Functional12\Event;

use Evoweb\Extender\Event\RegisterAutoloaderEvent;
use Evoweb\Extender\Loader\ClassLoader;
use Evoweb\Extender\Tests\Functional12\AbstractTestBase;
use PHPUnit\Framework\Attributes\Group;
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
        $autoloader = [GeneralUtility::getContainer()->get(ClassLoader::class), 'loadClass'];

        $subject = new class() extends RegisterAutoloaderEvent {
            public function __construct()
            {
            }

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

        $subject = new class() extends RegisterAutoloaderEvent {
            public function __construct()
            {
            }

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
        $register = new RegisterAutoloaderEvent(GeneralUtility::getContainer());

        $condition = $register->isPropagationStopped();

        self::assertTrue($condition);
    }
}
