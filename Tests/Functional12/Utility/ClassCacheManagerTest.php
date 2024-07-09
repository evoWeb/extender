<?php

namespace Evoweb\Extender\Tests\Functional12\Utility;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Evoweb\Extender\Utility\ClassCacheManager;
use Fixture\BaseExtension\Domain\Model\AnotherBlob;
use Fixture\BaseExtension\Domain\Model\Blob;
use Fixture\BaseExtension\Domain\Model\BlobWithStorage;
use Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

class ClassCacheManagerTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function parseSingleFile()
    {
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        /** @var ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['parseSingleFile'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(
            __DIR__ . '/../../Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php'
        );

        $expected = $this->getExpected(__FUNCTION__, $filePath);

        /** @noinspection PhpUndefinedMethodInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function parseSingleFileWithStorage()
    {
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['parseSingleFile', '_get'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader])
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

        /** @noinspection PhpUndefinedMethodInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        self::assertEquals($expected, $actual);
        self::assertEquals($expectedConstructorLines, $subject->_get('constructorLines'));
    }

    /**
     * @test
     */
    public function parseSingleFileWithStorageNotPsr2()
    {
        $cacheBackend = new FileBackend('production');
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        /** @var ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['parseSingleFile', '_get'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader])
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

        /** @noinspection PhpUndefinedMethodInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        self::assertEquals($expected, $actual);
        self::assertEquals($expectedConstructorLines, $subject->_get('constructorLines'));
    }

    /**
     * @test
     */
    public function changeCode()
    {
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        /** @var ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['changeCode'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $expected = $this->getExpected(__FUNCTION__, '--');

        /** @noinspection PhpUndefinedMethodInspection */
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
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        /** @var ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['getPartialInfo'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $expected = $this->getExpected(__FUNCTION__, '--');

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertEquals($expected, $subject->_call('getPartialInfo', '--'));
    }

    /**
     * @test
     */
    public function closeClassDefinition()
    {
        /** @var PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        /** @var ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(ClassCacheManager::class))
            ->onlyMethods(['closeClassDefinition'])
            ->setConstructorArgs([$cacheMock, $composerClassLoader])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $expected = '--' . chr(10) . '}';

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertEquals($expected, $subject->_call('closeClassDefinition', '--'));
    }

    /**
     * @test
     */
    public function reBuild()
    {
        $className = Blob::class;

        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

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
        $className = BlobWithStorage::class;

        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

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
        $className = AnotherBlob::class;

        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

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
        $className = BlobWithStorageAndConstructorArgument::class;

        $cacheBackend = new FileBackend('production');
        $cacheMock = new PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ComposerClassLoader::class);

        $subject = new ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $basePath = 'typo3conf/ext/base_extension/Classes/Domain/Model/BlobWithStorageAndConstructorArgument.php';
        $extendPath = 'typo3conf/ext/extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $expected = trim($this->getExpected(__FUNCTION__, $basePath, $extendPath));

        $actual = $cacheMock->get($cacheEntryIdentifier);

        self::assertEquals($expected, $actual);
    }
}