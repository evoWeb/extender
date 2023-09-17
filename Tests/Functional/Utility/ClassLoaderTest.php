<?php

namespace Evoweb\Extender\Tests\Functional\Utility;

use Evoweb\Extender\Utility\ClassCacheManager;
use Evoweb\Extender\Utility\ClassLoader;
use Fixture\BaseExtension\Domain\Model\Blob;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

class ClassLoaderTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function registerAutoloader()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager($cacheMock, $composerClassLoader, $register);

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

    /**
     * @test
     */
    public function isExcludedClassName()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager($cacheMock, $composerClassLoader, $register);

        /** @var ClassLoader|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassLoader::class))
            ->onlyMethods(['isExcludedClassName'])
            ->setConstructorArgs([$cacheMock, $classCacheManager, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        self::assertTrue($subject->_call('isExcludedClassName', 'Symfony\Polyfill\Mbstring\Mbstring'));
    }

    /**
     * @test
     */
    public function isValidClassName()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager($cacheMock, $composerClassLoader, $register);

        /** @var ClassLoader|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassLoader::class))
            ->onlyMethods(['isValidClassName'])
            ->setConstructorArgs([$cacheMock, $classCacheManager, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        self::assertTrue($subject->_call('isValidClassName', Blob::class));
    }

    /**
     * @test
     */
    public function loadClass()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend|MockObject $cacheMock */
        $cacheMock = $this->getMockBuilder(PhpFrontend::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $cacheMock->expects(self::any())->method('has')->willReturn(true);
        $cacheMock->expects(self::any())->method('requireOnce')->willReturn(true);

        $classCacheManager = new ClassCacheManager($cacheMock, $composerClassLoader, $register);
        $subject = new ClassLoader($cacheMock, $classCacheManager, $register);

        $condition = $subject->loadClass(Blob::class);

        self::assertTrue($condition);
    }

    /**
     * @test
     */
    public function extendedClassIsOfBaseType()
    {
        self::assertEquals(Blob::class, get_class(new Blob()));
    }

    /**
     * @test
     */
    public function extendedClassHasOtherProperty()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager($cacheMock, $composerClassLoader, $register);

        $subject = new ClassLoader($cacheMock, $classCacheManager, $register);
        $subject->loadClass(Blob::class);
        $condition = property_exists(Blob::class, 'otherProperty');

        self::assertTrue($condition);
    }
}
