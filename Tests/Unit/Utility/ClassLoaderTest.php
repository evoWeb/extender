<?php
namespace Evoweb\Extender\Tests\Unit\Utility;

use Evoweb\Extender\Utility\ClassLoader;

class ClassLoaderTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function registerAutoloader()
    {
        $this->resetSingletonInstances = true;

        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        $subject = new \Evoweb\Extender\Utility\ClassLoader($cacheMock);
        $subject::registerAutoloader();

        $autoloaders = spl_autoload_functions();

        $condition = true;
        foreach ($autoloaders as $autoloader) {
            $classLoader = $autoloader[0];
            if ((is_string($classLoader) && $classLoader == ClassLoader::class)
                || (is_object($classLoader) && get_class($classLoader) == ClassLoader::class)
            ) {
                $condition = true;
                break;
            }
        }

        $this->assertTrue($condition);
    }

    /**
     * @test
     */
    public function isExcludedClassName()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
            ->onlyMethods(['isExcludedClassName'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertTrue($subject->_call('isExcludedClassName', 'Symfony\Polyfill\Mbstring\Mbstring'));
    }

    /**
     * @test
     */
    public function getExtensionKey()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
            ->onlyMethods(['getExtensionKey'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(
            'base_extension',
            $subject->_call('getExtensionKey', 'Fixture\BaseExtension\Domain\Model\Blob')
        );
    }

    /**
     * @test
     */
    public function isValidClassName()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
            ->onlyMethods(['isValidClassName'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        /** @noinspection PhpUndefinedMethodInspection */
        $extension = $subject->_call('getExtensionKey', $className);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertTrue($subject->_call('isValidClassName', $className, $extension));
    }

    /**
     * @test
     */
    public function loadClass()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend|\\PHPUnit\Framework\MockObject\MockObject $cacheMock */
        $cacheMock = $this->getMockBuilder(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $cacheMock->expects($this->any())->method('has')->will($this->returnValue(true));
        $cacheMock->expects($this->any())->method('requireOnce')->will($this->returnValue(true));

        $subject = new \Evoweb\Extender\Utility\ClassLoader($cacheMock);

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        $condition = $subject->loadClass($className);

        $this->assertTrue($condition);
    }

    /**
     * @test
     */
    public function extendedClassIsOfBaseType()
    {
        $actual = new \Fixture\BaseExtension\Domain\Model\Blob();

        $this->assertEquals(\Fixture\BaseExtension\Domain\Model\Blob::class, get_class($actual));
    }

    /**
     * @test
     */
    public function extendedClassHasOtherProperty()
    {
        $subject = new \Fixture\BaseExtension\Domain\Model\Blob();
        $condition = property_exists($subject, 'otherProperty');

        $this->assertTrue($condition);
    }
}
