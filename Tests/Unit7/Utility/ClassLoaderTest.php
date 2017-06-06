<?php
namespace Evoweb\Extender\Tests\Unit7\Utility;

use Evoweb\Extender\Utility\ClassLoader;

class ClassLoaderTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function registerAutoloader()
    {
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

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
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
            ->setMethods(['isExcludedClassName'])
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
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
            ->setMethods(['getExtensionKey'])
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
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassLoader $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassLoader::class))
            ->setMethods(['isValidClassName'])
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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend|\PHPUnit_Framework_MockObject_MockObject $cacheMock */
        $cacheMock = $this->getMockBuilder(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
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
