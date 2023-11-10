<?php

namespace Evoweb\Extender\Tests\Functional\Utility;

use Evoweb\Extender\Cache\CacheFactory;
use Evoweb\Extender\Composer\ClassComposer;
use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Cache\ClassCacheManager;
use Evoweb\Extender\Loader\ClassLoader;
use Evoweb\Extender\Parser\ClassParser;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use Fixture\BaseExtension\Domain\Model\Blob;
use PhpParser\ParserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;

class ClassLoaderTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function loadClass(): void
    {
        $register = new Register(['test' => []]);
        /** @var ClassCacheManager $classCacheManager */
        $classCacheManager = $this->createMock(ClassCacheManager::class);

        /** @var FileBackend $cacheBackend */
        $cacheBackend = $this->createMock(FileBackend::class);
        /** @var PhpFrontend|MockObject $cacheMock */
        $cacheMock = $this->getMockBuilder(PhpFrontend::class)
            ->setConstructorArgs(['extender', $cacheBackend])
            ->getMock();
        $cacheMock->expects($this->once())->method('has')->willReturn(true);
        $cacheMock->expects($this->once())->method('requireOnce')->willReturn(true);

        $subject = new ClassLoader($cacheMock, $classCacheManager, $register);

        $condition = $subject->loadClass('test');

        self::assertTrue($condition);
    }

    /**
     * @test
     */
    public function isValidClassName(): void
    {
        $register = new Register(['test' => []]);
        /** @var ClassCacheManager $classCacheManager */
        $classCacheManager = $this->createMock(ClassCacheManager::class);
        /** @var PhpFrontend $classCache */
        $classCache = $this->getMockBuilder(PhpFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new class($classCache, $classCacheManager, $register) extends ClassLoader {
            public function isValidClassName(string $className): bool
            {
                return parent::isValidClassName($className);
            }
        };

        $condition = $subject->isValidClassName('test');

        self::assertTrue($condition);
    }

    // todo
    // disable due to Blob is loaded in setup and can not be loaded again
    /**
     * @ test
     * @ group selected
     */
    public function extendedClassIsOfBaseType(): void
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheManager = new CacheFactory();
        $classCache = $cacheManager->createCache('extender');
        $parserFactory = new ParserFactory();
        $classParser = new ClassParser($parserFactory);
        $classComposer = new ClassComposer();
        $classCacheManager = new ClassCacheManager(
            $classCache,
            $composerClassLoader,
            $classParser,
            $classComposer,
            $register
        );

        $expected = 'Fixture\BaseExtension\Domain\Model\Blob';
        $subject = new ClassLoader($classCache, $classCacheManager, $register);
        $subject->loadClass($expected);

        $actual = get_class(new Blob());

        self::assertEquals($expected, $actual);
    }

    // todo
    // disable due to Blob is loaded in setup and can not be loaded again
    /**
     * @ test
     * @ group selected
     */
    public function extendedClassHasOtherProperty(): void
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheManager = new CacheFactory();
        $classCache = $cacheManager->createCache('extender');
        $parserFactory = new ParserFactory();
        $classParser = new ClassParser($parserFactory);
        $classComposer = new ClassComposer();
        $classCacheManager = new ClassCacheManager(
            $classCache,
            $composerClassLoader,
            $classParser,
            $classComposer,
            $register
        );

        $className = 'Fixture\BaseExtension\Domain\Model\Blob';
        $subject = new ClassLoader($classCache, $classCacheManager, $register);
        $subject->loadClass($className);

        $blob = new Blob();
        $condition = property_exists($blob, 'otherProperty');

        self::assertTrue($condition);
    }
}
