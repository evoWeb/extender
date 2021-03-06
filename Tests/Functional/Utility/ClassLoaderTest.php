<?php

namespace Evoweb\Extender\Tests\Functional\Utility;

use Evoweb\Extender\Utility\ClassLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassLoaderTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function registerAutoloader()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);
        $classCacheManager = new \Evoweb\Extender\Utility\ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(\Composer\Autoload\ClassLoader::class)
        );

        $subject = new \Evoweb\Extender\Utility\ClassLoader($cacheMock, $classCacheManager);
        $subject::registerAutoloader();

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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);
        $classCacheManager = new \Evoweb\Extender\Utility\ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(\Composer\Autoload\ClassLoader::class)
        );

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);
        $classCacheManager = new \Evoweb\Extender\Utility\ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(\Composer\Autoload\ClassLoader::class)
        );

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);
        $classCacheManager = new \Evoweb\Extender\Utility\ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(\Composer\Autoload\ClassLoader::class)
        );

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
            ->onlyMethods(['isValidClassName'])
            ->setConstructorArgs([$cacheMock, $classCacheManager])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend|\PHPUnit\Framework\MockObject\MockObject $cacheMock */
        $cacheMock = $this->getMockBuilder(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $cacheMock->expects(self::any())->method('has')->willReturn(true);
        $cacheMock->expects(self::any())->method('requireOnce')->willReturn(true);

        $classCacheManager = new \Evoweb\Extender\Utility\ClassCacheManager(
            $cacheMock,
            GeneralUtility::getContainer()->get(\Composer\Autoload\ClassLoader::class)
        );

        $subject = new \Evoweb\Extender\Utility\ClassLoader($cacheMock, $classCacheManager);

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        $condition = $subject->loadClass($className);

        self::assertTrue($condition);
    }

    /**
     * @test
     */
    public function extendedClassIsOfBaseType()
    {
        $actual = new \Fixture\BaseExtension\Domain\Model\Blob();

        self::assertEquals(\Fixture\BaseExtension\Domain\Model\Blob::class, get_class($actual));
    }

    /**
     * @test
     */
    public function extendedClassHasOtherProperty()
    {
        $subject = new \Fixture\BaseExtension\Domain\Model\Blob();
        $condition = property_exists($subject, 'otherProperty');

        self::assertTrue($condition);
    }
}
