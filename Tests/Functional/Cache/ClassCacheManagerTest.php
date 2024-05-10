<?php

namespace Evoweb\Extender\Tests\Functional\Cache;

use Composer\Autoload\ClassLoader;
use Evoweb\Extender\Cache\ClassCacheManager;
use Evoweb\Extender\Composer\ClassComposer;
use Evoweb\Extender\Configuration\ClassRegister;
use Evoweb\Extender\Exception\BaseFileNotFoundException;
use Evoweb\Extender\Parser\ClassParser;
use Evoweb\Extender\Parser\FileSegments;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use Fixture\BaseExtension\Domain\Model\Blob;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class ClassCacheManagerTest extends AbstractTestBase
{
    #[Test]
    public function build(): void
    {
        $classCache = $this->createMock(FrontendInterface::class);
        $classLoader = $this->createMock(ClassLoader::class);
        $classParser = $this->createMock(ClassParser::class);
        $classComposer = $this->createMock(ClassComposer::class);
        $classRegister = $this->createMock(ClassRegister::class);

        /** @var ClassCacheManager|MockObject $subject */
        $subject = $this->getMockBuilder(ClassCacheManager::class)
            ->onlyMethods([
                'getBaseClassFileSegments',
                'getExtendingClassesFileSegments',
                'getMergedFileCode',
                'addFileToCache'
            ])
            ->setConstructorArgs([$classCache, $classLoader, $classParser, $classComposer, $classRegister])
            ->getMock();

        $subject->expects($this->once())->method('getBaseClassFileSegments')->willReturn(new FileSegments());
        $subject->expects($this->once())->method('getExtendingClassesFileSegments')->willReturn([]);
        $subject->expects($this->once())->method('getMergedFileCode')->willReturn('');
        $subject->expects($this->once())->method('addFileToCache');

        $subject->build('test', Blob::class);
    }

    #[Test]
    public function getBaseClassFileSegments(): void
    {
        $classCache = $this->createMock(FrontendInterface::class);
        $classLoader = $this->createMock(ClassLoader::class);
        $classParser = $this->createMock(ClassParser::class);
        $classComposer = $this->createMock(ClassComposer::class);
        $classRegister = $this->createMock(ClassRegister::class);

        $subject = new class (
            $classCache,
            $classLoader,
            $classParser,
            $classComposer,
            $classRegister
        ) extends ClassCacheManager {
            public function getBaseClassFileSegments(string $className): FileSegments
            {
                return parent::getBaseClassFileSegments($className);
            }

            protected function getFileSegments(
                string $className,
                bool $baseClass,
                string $exceptionClass
            ): FileSegments {
                return new FileSegments();
            }
        };

        $expected = new FileSegments();

        $actual = $subject->getBaseClassFileSegments('test');

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function getExtendingClassesFileSegments(): void
    {
        $classCache = $this->createMock(FrontendInterface::class);
        $classLoader = $this->createMock(ClassLoader::class);
        $classParser = $this->createMock(ClassParser::class);
        $classComposer = $this->createMock(ClassComposer::class);

        $classRegister = $this->createMock(ClassRegister::class);
        $classRegister->expects($this->once())->method('getExtendingClasses')->willReturn(['test2', 'test3']);

        $subject = new class (
            $classCache,
            $classLoader,
            $classParser,
            $classComposer,
            $classRegister
        ) extends ClassCacheManager {
            public function getExtendingClassesFileSegments(string $baseClassName): array
            {
                return parent::getExtendingClassesFileSegments($baseClassName);
            }

            protected function getFileSegments(string $className, bool $baseClass, string $exceptionClass): FileSegments
            {
                return new FileSegments();
            }
        };

        $expected = [
            new FileSegments(),
            new FileSegments()
        ];

        $actual = $subject->getExtendingClassesFileSegments('test');

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function getFileSegments(): void
    {
        $classCache = $this->createMock(FrontendInterface::class);
        $classComposer = $this->createMock(ClassComposer::class);
        $classRegister = $this->createMock(ClassRegister::class);

        $basePath = realpath(
            __DIR__ . '/../../Fixtures/Extensions/base_extension/Classes/Domain/Model/GetFileSegments.php'
        );

        $ClassLoader = $this->createMock(ClassLoader::class);
        $ClassLoader->expects($this->once())->method('findFile')->willReturn($basePath);

        $expected = new FileSegments();
        $expected->setBaseClass(true);

        $classParser = $this->createMock(ClassParser::class);
        $classParser->expects($this->once())->method('getFileSegments')->willReturn($expected);

        $subject = new class (
            $classCache,
            $ClassLoader,
            $classParser,
            $classComposer,
            $classRegister
        ) extends ClassCacheManager {
            public function getFileSegments(string $className, bool $baseClass, string $exceptionClass): FileSegments
            {
                return parent::getFileSegments($className, $baseClass, $exceptionClass);
            }
        };

        $actual = $subject->getFileSegments('test', true, BaseFileNotFoundException::class);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function getMergedFileCode(): void
    {
        $classCache = $this->createMock(FrontendInterface::class);
        $classLoader = $this->createMock(ClassLoader::class);
        $classParser = $this->createMock(ClassParser::class);
        $classRegister = $this->createMock(ClassRegister::class);

        $classComposer = $this->createMock(ClassComposer::class);
        $classComposer->expects($this->once())->method('composeMergedFileCode')->willReturn('');

        $subject = new class (
            $classCache,
            $classLoader,
            $classParser,
            $classComposer,
            $classRegister
        ) extends ClassCacheManager {
            public function getMergedFileCode(array $fileSegments): string
            {
                return parent::getMergedFileCode($fileSegments);
            }
        };

        $expected = '';

        $actual = $subject->getMergedFileCode([]);

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function addFileToCache(): void
    {
        $classLoader = $this->createMock(ClassLoader::class);
        $classParser = $this->createMock(ClassParser::class);
        $classComposer = $this->createMock(ClassComposer::class);
        $classRegister = $this->createMock(ClassRegister::class);

        $classCache = $this->createMock(FrontendInterface::class);
        $classCache->expects($this->once())->method('set')->willReturn(null);

        $subject = new class (
            $classCache,
            $classLoader,
            $classParser,
            $classComposer,
            $classRegister
        ) extends ClassCacheManager {
            public function addFileToCache(string $cacheEntryIdentifier, string $code): void
            {
                parent::addFileToCache($cacheEntryIdentifier, $code);
            }
        };

        $subject->addFileToCache('testIdentifier', 'testCode');
    }
}
