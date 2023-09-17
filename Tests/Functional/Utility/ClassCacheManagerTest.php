<?php

namespace Evoweb\Extender\Tests\Functional\Utility;

use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Utility\ClassCacheManager;
use Fixture\BaseExtension\Domain\Model\AnotherBlob;
use Fixture\BaseExtension\Domain\Model\Blob;
use Fixture\BaseExtension\Domain\Model\BlobWithStorage;
use Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

class ClassCacheManagerTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function parseSingleFile()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['parseSingleFile'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(__DIR__ . '/../../Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php');

        $expected = $this->getExpected(__FUNCTION__, $filePath);

        $actual = $subject->_call('parseSingleFile', $filePath);

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function parseSingleFileWithStorage()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['parseSingleFile', '_get'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(
            __DIR__ . '/../../Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorage.php'
        );

        $expected = $this->getExpected(__FUNCTION__, $filePath);
        $expectedConstructorLines = [
            '    public function __construct()',
            '    {',
            '        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();'
        ];

        $actual = $subject->_call('parseSingleFile', $filePath);
        $actualConstructorLines = $subject->_get('constructorLines');

        self::assertEquals($expected, $actual);
        self::assertEquals($expectedConstructorLines, $actualConstructorLines);
    }

    /**
     * @test
     */
    public function parseSingleFileWithStorageNotPsr2()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            PhpFrontend::class,
            [],
            ['extender', new FileBackend('production')]
        );

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['parseSingleFile', '_get'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(
            __DIR__ . '/../../Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorageNotPsr2.php'
        );

        $expected = $this->getExpected(__FUNCTION__, $filePath);
        $expectedConstructorLines = [
            '    public function __construct()',
            '    {',
            '        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();'
        ];

        $actual = $subject->_call('parseSingleFile', $filePath);
        $actualConstructorLines = $subject->_get('constructorLines');

        self::assertEquals($expected, $actual);
        self::assertEquals($expectedConstructorLines, $actualConstructorLines);
    }

    /**
     * @test
     */
    public function changeCode()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['changeCode'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $expected = $this->getExpected(__FUNCTION__, '--');

        $actual = $subject->_call('changeCode', '<?php
namespace Fixture\BaseExtension\Domain\Model;

class Blob extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $test = \'\';
}
', '--');

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getPartialInfo()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['getPartialInfo'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $expected = $this->getExpected(__FUNCTION__, '--');

        $actual = $subject->_call('getPartialInfo', '--');

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function closeClassDefinition()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['closeClassDefinition'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader, $register])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $expected = '--' . chr(10) . '}';

        $actual = $subject->_call('closeClassDefinition', '--');

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function build()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $className = Blob::class;
        $cacheEntryIdentifier = str_replace('\\', '_', $className);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader, $register);
        $subject->build($cacheEntryIdentifier, $className);

        $basePath = 'typo3conf/ext/base_extension/Classes/Domain/Model/Blob.php';
        $extendPath = 'typo3conf/ext/extending_extension/Classes/Domain/Model/BlobExtend.php';

        $expected = trim($this->getExpected(__FUNCTION__, $basePath, $extendPath));

        $actual = $cacheMock->get($cacheEntryIdentifier);

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function reBuildWithStorage()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $className = BlobWithStorage::class;
        $cacheEntryIdentifier = str_replace('\\', '_', $className);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader, $register);
        $subject->build($cacheEntryIdentifier, $className);

        $basePath = 'typo3conf/ext/base_extension/Classes/Domain/Model/BlobWithStorage.php';
        $extendPath = 'typo3conf/ext/extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $expected = trim($this->getExpected(__FUNCTION__, $basePath, $extendPath));

        $actual = $cacheMock->get($cacheEntryIdentifier);

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function reBuildNonStorageWithStorage()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $className = AnotherBlob::class;
        $cacheEntryIdentifier = str_replace('\\', '_', $className);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader, $register);
        $subject->build($cacheEntryIdentifier, $className);

        $basePath = 'typo3conf/ext/base_extension/Classes/Domain/Model/AnotherBlob.php';
        $extendPath = 'typo3conf/ext/extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $expected = trim($this->getExpected(__FUNCTION__, $basePath, $extendPath));

        $actual = $cacheMock->get($cacheEntryIdentifier);

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function reBuildWithStorageAndConstructorArgument()
    {
        $composerClassLoader = $this->getComposerClassLoader();
        $register = $this->getRegister();
        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $className = BlobWithStorageAndConstructorArgument::class;
        $cacheEntryIdentifier = str_replace('\\', '_', $className);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader, $register);
        $subject->build($cacheEntryIdentifier, $className);

        $basePath = 'typo3conf/ext/base_extension/Classes/Domain/Model/BlobWithStorageAndConstructorArgument.php';
        $extendPath = 'typo3conf/ext/extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $expected = trim($this->getExpected(__FUNCTION__, $basePath, $extendPath));

        $actual = $cacheMock->get($cacheEntryIdentifier);

        self::assertEquals($expected, $actual);
    }
}
