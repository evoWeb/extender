<?php

namespace Evoweb\Extender\Tests\Functional\Utility;

use Evoweb\Extender\Cache\CacheFactory;
use Evoweb\Extender\Composer\ClassComposer;
use Evoweb\Extender\Configuration\ClassRegister;
use Evoweb\Extender\Cache\ClassCacheManager;
use Evoweb\Extender\Loader\ClassLoader;
use Evoweb\Extender\Parser\ClassParser;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use Fixture\BaseExtension\Domain\Model\Blob;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;

class ClassLoaderTest extends AbstractTestBase
{
    #[Test]
    public function loadClass(): void
    {
        $classRegister = new ClassRegister(['test' => []]);
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

        $subject = new ClassLoader($cacheMock, $classCacheManager, $classRegister);

        $condition = $subject->loadClass('test');

        self::assertTrue($condition);
    }

    #[Test]
    public function isValidClassName(): void
    {
        $classRegister = new ClassRegister(['test' => []]);
        /** @var ClassCacheManager $classCacheManager */
        $classCacheManager = $this->createMock(ClassCacheManager::class);
        /** @var PhpFrontend|MockObject $classCache */
        $classCache = $this->getMockBuilder(PhpFrontend::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new class ($classCache, $classCacheManager, $classRegister) extends ClassLoader {
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
    // #[Test]
    // #[Group('selected')]
    public function extendedClassIsOfBaseType(): void
    {
        $classLoader = $this->getClassLoader();
        $classRegister = $this->getClassRegister();
        $cacheManager = new CacheFactory();
        $classCache = $cacheManager->createCache('extender');
        $parserFactory = new ParserFactory();
        $classParser = new ClassParser($parserFactory);
        $classComposer = new ClassComposer();
        $classCacheManager = new ClassCacheManager(
            $classCache,
            $classLoader,
            $classParser,
            $classComposer,
            $classRegister
        );

        $expected = 'Fixture\BaseExtension\Domain\Model\Blob';
        $subject = new ClassLoader($classCache, $classCacheManager, $classRegister);
        $subject->loadClass($expected);

        $actual = get_class(new Blob());

        self::assertEquals($expected, $actual);
    }

    // todo
    // disable due to Blob is loaded in setup and can not be loaded again
    // #[Test]
    // #[Group('selected')]
    public function extendedClassHasOtherProperty(): void
    {
        $classLoader = $this->getClassLoader();
        $classRegister = $this->getClassRegister();
        $cacheManager = new CacheFactory();
        $classCache = $cacheManager->createCache('extender');
        $parserFactory = new ParserFactory();
        $classParser = new ClassParser($parserFactory);
        $classComposer = new ClassComposer();
        $classCacheManager = new ClassCacheManager(
            $classCache,
            $classLoader,
            $classParser,
            $classComposer,
            $classRegister
        );

        $className = 'Fixture\BaseExtension\Domain\Model\Blob';
        $subject = new ClassLoader($classCache, $classCacheManager, $classRegister);
        $subject->loadClass($className);

        $blob = new Blob();
        $condition = property_exists($blob, 'otherProperty');

        self::assertTrue($condition);
    }
}
