<?php

namespace Evoweb\Extender\Tests\Functional12\Utility;

use Composer\Autoload\ClassLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

class ClassCacheManagerTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function parseSingleFile()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
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

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        self::assertEquals($expected, $actual);
        self::assertEquals($expectedConstructorLines, $subject->_get('constructorLines'));
    }

    /**
     * @test
     */
    public function parseSingleFileWithStorageNotPsr2()
    {
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
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

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        self::assertEquals($expected, $actual);
        self::assertEquals($expectedConstructorLines, $subject->_get('constructorLines'));
    }

    /**
     * @test
     */
    public function changeCode()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
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
        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
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
        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
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
        $className = \Fixture\BaseExtension\Domain\Model\AnotherBlob::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
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
        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
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
