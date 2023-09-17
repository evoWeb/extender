<?php

namespace Evoweb\Extender\Tests\Functional12\Utility;

use Evoweb\Extender\Cache\CacheManager;
use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Utility\ClassCacheManager;
use Evoweb\Extender\Utility\ClassLoader;
use Fixture\BaseExtension\Domain\Model\Blob;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

class ClassLoaderTest extends AbstractTestBase
{
    #[Test]
    public function registerAutoloader()
    {
        /** @var Register $register */
        $register = $this->createMock(Register::class);
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        /** @var ClassCacheManager $classCacheManager */
        $classCacheManager = $this->createMock(ClassCacheManager::class);

        $subject = new ClassLoader($cacheMock, $classCacheManager, $register);
        spl_autoload_register([$subject, 'loadClass'], true, true);

        $autoLoaders = spl_autoload_functions();

        $condition = false;
        foreach ($autoLoaders as $autoloader) {
            $classLoader = $autoloader[0];
            if (
                (is_string($classLoader) && $classLoader == ClassLoader::class)
                || (is_object($classLoader) && get_class($classLoader) == ClassLoader::class)
            ) {
                $condition = true;
                break;
            }
        }

        self::assertTrue($condition);
    }

    #[Test]
    public function isExcludedClassName()
    {
        /** @var Register $register */
        $register = $this->createMock(Register::class);
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        /** @var ClassCacheManager $classCacheManager */
        $classCacheManager = $this->createMock(ClassCacheManager::class);

        /** @var ClassLoader|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassLoader::class))
            ->onlyMethods(['isExcludedClassName'])
            ->setConstructorArgs([$cacheMock, $classCacheManager, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $condition = $subject->_call('isExcludedClassName', 'Symfony\Polyfill\Mbstring\Mbstring');

        self::assertTrue($condition);
    }

    #[Test]
    public function isValidClassName()
    {
        $register = new Register();
        $register->setExtendedClasses(['test' => []]);
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        /** @var ClassCacheManager $classCacheManager */
        $classCacheManager = $this->createMock(ClassCacheManager::class);

        /** @var ClassLoader|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassLoader::class))
            ->onlyMethods(['isValidClassName'])
            ->setConstructorArgs([$cacheMock, $classCacheManager, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $condition = $subject->_call('isValidClassName', 'test');

        self::assertTrue($condition);
    }

    #[Test]
    public function loadClass()
    {
        $register = new Register();
        $register->setExtendedClasses(['test' => []]);
        /** @var ClassCacheManager $classCacheManager */
        $classCacheManager = $this->createMock(ClassCacheManager::class);
        $cacheBackend = new FileBackend('production');
        /** @var PhpFrontend|MockObject $cacheMock */
        $cacheMock = $this->getMockBuilder(PhpFrontend::class)
            ->setConstructorArgs(['extender', $cacheBackend])
            ->disableOriginalClone()
            ->getMock();

        $cacheMock->expects(self::any())->method('has')->willReturn(true);
        $cacheMock->expects(self::any())->method('requireOnce')->willReturn(true);

        $subject = new ClassLoader($cacheMock, $classCacheManager, $register);

        $condition = $subject->loadClass('test');

        self::assertTrue($condition);
    }

    #[Test]
    public function extendedClassIsOfBaseType()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheManager = new CacheManager();
        $cacheMock = $cacheManager->createCache('extender');
        $classCacheManager = new ClassCacheManager($cacheMock, $composerClassLoader, $register);

        $expected = 'Fixture\BaseExtension\Domain\Model\Blob';
        $subject = new ClassLoader($cacheMock, $classCacheManager, $register);
        $subject->loadClass($expected);

        $actual = get_class(new Blob());

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function extendedClassHasOtherProperty()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheManager = new CacheManager();
        $cacheMock = $cacheManager->createCache('extender');
        $classCacheManager = new ClassCacheManager($cacheMock, $composerClassLoader, $register);

        $className = 'Fixture\BaseExtension\Domain\Model\Blob';
        $subject = new ClassLoader($cacheMock, $classCacheManager, $register);
        $subject->loadClass($className);

        $blob = new Blob();
        $condition = property_exists($blob, 'otherProperty');

        self::assertTrue($condition);
    }
}
