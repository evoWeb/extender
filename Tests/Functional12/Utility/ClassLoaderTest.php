<?php

namespace Evoweb\Extender\Tests\Functional12\Utility;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Evoweb\Extender\Utility\ClassCacheManager;
use Evoweb\Extender\Utility\ClassLoader;
use Fixture\BaseExtension\Domain\Model\Blob;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassLoaderTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function registerAutoloader()
    {
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(ComposerClassLoader::class)
        );

        $subject = new ClassLoader($cacheMock, $classCacheManager);
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
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(ComposerClassLoader::class)
        );

        /** @var ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassLoader::class))
            ->onlyMethods(['isExcludedClassName'])
            ->setConstructorArgs([$cacheMock, $classCacheManager])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertTrue($subject->_call('isExcludedClassName', 'Symfony\Polyfill\Mbstring\Mbstring'));
    }

    /**
     * @test
     */
    public function getExtensionKeyFromNamespace()
    {
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(ComposerClassLoader::class)
        );

        /** @var ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassLoader::class))
            ->onlyMethods(['getExtensionKeyFromNamespace'])
            ->setConstructorArgs([$cacheMock, $classCacheManager])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertEquals(
            'base_extension',
            $subject->_call('getExtensionKeyFromNamespace', 'Fixture\BaseExtension\Domain\Model\Blob')
        );
    }

    /**
     * @test
     */
    public function isValidClassName()
    {
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);
        $classCacheManager = new ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(ComposerClassLoader::class)
        );

        /** @var ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassLoader::class))
            ->onlyMethods(['isValidClassName'])
            ->setConstructorArgs([$cacheMock, $classCacheManager])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $className = Blob::class;
        /** @noinspection PhpUndefinedMethodInspection */
        $extension = $subject->_call('getExtensionKeyFromNamespace', $className);

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertTrue($subject->_call('isValidClassName', $className, $extension));
    }

    /**
     * @test
     */
    public function loadClass()
    {
        /** @var PhpFrontend|MockObject $cacheMock */
        $cacheMock = $this->getMockBuilder(PhpFrontend::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $cacheMock->expects(self::any())->method('has')->willReturn(true);
        $cacheMock->expects(self::any())->method('requireOnce')->willReturn(true);

        $classCacheManager = new ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(ComposerClassLoader::class)
        );

        $subject = new ClassLoader($cacheMock, $classCacheManager);

        $className = Blob::class;
        $condition = $subject->loadClass($className);

        self::assertTrue($condition);
    }

    /**
     * @test
     */
    public function extendedClassIsOfBaseType()
    {
        $actual = new Blob();

        self::assertEquals(Blob::class, get_class($actual));
    }

    /**
     * @test
     */
    public function extendedClassHasOtherProperty()
    {
        $subject = new Blob();
        $condition = property_exists($subject, 'otherProperty');

        self::assertTrue($condition);
    }
}
