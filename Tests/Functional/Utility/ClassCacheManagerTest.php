<?php
namespace Evoweb\Extender\Tests\Functional\Utility;

use Composer\Autoload\ClassLoader;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Core\Environment;
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

        $filePath = realpath(__DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php');

        $expected = '/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $filePath) . '
***********************************************************************/
    /**
     * @var string
     */
    protected $property = \'\';

    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

';

        /** @noinspection PhpUndefinedMethodInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        $this->assertEquals($expected, $actual);
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
            __DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorage.php'
        );

        $expected = '/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $filePath) . '
***********************************************************************/
    /**
     * @var string
     */
    protected $property = \'\';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $storage = \'\';


    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

';
        $expectedConstructorLines = [
            '    public function __construct()',
            '    {',
            '        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();'
        ];

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        $this->assertEquals($expected, $actual);
        $this->assertEquals($expectedConstructorLines, $subject->_get('constructorLines'));
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
            __DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorageNotPsr2.php'
        );

        $expected = '/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $filePath) . '
***********************************************************************/
    /**
     * @var string
     */
    protected $property = \'\';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $storage = \'\';


    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property) {
        $this->property = $property;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage
     */
    public function setStorage($storage) {
        $this->storage = $storage;
    }

';

        $expectedConstructorLines = [
            '    public function __construct() {',
            '        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();'
        ];

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $actual = $subject->_call('parseSingleFile', $filePath);

        $this->assertEquals($expected, $actual);
        $this->assertEquals($expectedConstructorLines, $subject->_get('constructorLines'));
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

        $expected = '/***********************************************************************
 * this is partial from:
 *  --
***********************************************************************/
    /**
     * @var string
     */
    protected $test = \'\';

';

        /** @noinspection PhpUndefinedMethodInspection */
        $actual = $subject->_call('changeCode', '<?php
namespace Fixture\BaseExtension\Domain\Model;

class Blob extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $test = \'\';
}
', '--');

        $this->assertEquals($expected, $actual);
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

        $expected = '/***********************************************************************' . chr(10)
            . ' * this is partial from:' . chr(10) . ' *  --' . chr(10)
            . '***********************************************************************/' . chr(10);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($expected, $subject->_call('getPartialInfo', '--'));
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
        $this->assertEquals($expected, $subject->_call('closeClassDefinition', '--'));
    }

    /**
     * @test
     */
    public function reBuild()
    {
        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $basePath = realpath(__DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php');
        $extendPath = realpath(
            __DIR__ . '/../Fixtures/Extensions/extending_extension/Classes/Extending/Model/BlobExtend.php'
        );

        $expected = '<?php
/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $basePath) . '
***********************************************************************/

namespace Fixture\BaseExtension\Domain\Model;

class Blob extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $property = \'\';

    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $extendPath) . '
***********************************************************************/
    /**
     * @var int
     */
    protected $otherProperty = 0;

    /**
     * Getter for otherProperty
     *
     * @return int
     */
    public function getOtherProperty()
    {
        return $this->otherProperty;
    }

    /**
     * Setter for otherProperty
     *
     * @param int $otherProperty
     *
     * @return void
     */
    public function setOtherProperty($otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }


}
#';

        $actual = $cacheMock->get($cacheEntryIdentifier);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function reBuildWithStorage()
    {
        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $basePath = realpath(
            __DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorage.php'
        );
        $extendPath = realpath(
            __DIR__ . '/../Fixtures/Extensions/extending_extension/Classes/Extending/Model/BlobWithStorageExtend.php'
        );

        $expected = '<?php
/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $basePath) . '
***********************************************************************/

namespace Fixture\BaseExtension\Domain\Model;

class BlobWithStorage extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $property = \'\';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $storage = \'\';


    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $extendPath) . '
***********************************************************************/
    /**
     * @var int
     */
    protected $otherProperty = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $otherStorage = \'\';


    /**
     * Getter for otherProperty
     *
     * @return int
     */
    public function getOtherProperty()
    {
        return $this->otherProperty;
    }

    /**
     * Setter for otherProperty
     *
     * @param int $otherProperty
     *
     * @return void
     */
    public function setOtherProperty($otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getOtherStorage()
    {
        return $this->otherStorage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $otherStorage
     */
    public function setOtherStorage($otherStorage)
    {
        $this->otherStorage = $otherStorage;
    }

    public function __construct()
    {
        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->otherStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

}
#';

        $actual = $cacheMock->get($cacheEntryIdentifier);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function reBuildNonStorageWithStorage()
    {
        $className = \Fixture\BaseExtension\Domain\Model\AnotherBlob::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $fixtureFolder = __DIR__ . '/../Fixtures/Extensions/';
        $basePath = realpath($fixtureFolder . 'base_extension/Classes/Domain/Model/AnotherBlob.php');
        $extendPath = realpath(
            $fixtureFolder . 'extending_extension/Classes/Extending/Model/BlobWithStorageExtend.php'
        );

        $expected = '<?php
/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $basePath) . '
***********************************************************************/

namespace Fixture\BaseExtension\Domain\Model;

class AnotherBlob extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $property = \'\';

    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $extendPath) . '
***********************************************************************/
    /**
     * @var int
     */
    protected $otherProperty = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $otherStorage = \'\';


    /**
     * Getter for otherProperty
     *
     * @return int
     */
    public function getOtherProperty()
    {
        return $this->otherProperty;
    }

    /**
     * Setter for otherProperty
     *
     * @param int $otherProperty
     *
     * @return void
     */
    public function setOtherProperty($otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getOtherStorage()
    {
        return $this->otherStorage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $otherStorage
     */
    public function setOtherStorage($otherStorage)
    {
        $this->otherStorage = $otherStorage;
    }

    public function __construct()
    {
        $this->otherStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

}
#';

        $actual = $cacheMock->get($cacheEntryIdentifier);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function reBuildWithStorageAndConstructorArgument()
    {
        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class;

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $composerClassLoader = GeneralUtility::getContainer()->get(ClassLoader::class);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock, $composerClassLoader);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $basePath = realpath(
            __DIR__ .
            '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorageAndConstructorArgument.php'
        );
        $extendPath = realpath(
            __DIR__ . '/../Fixtures/Extensions/extending_extension/Classes/Extending/Model/BlobWithStorageExtend.php'
        );

        $expected = '<?php
/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $basePath) . '
***********************************************************************/

namespace Fixture\BaseExtension\Domain\Model;

class BlobWithStorageAndConstructorArgument extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $property = \'\';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $storage = \'\';


    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

/***********************************************************************
 * this is partial from:
 *  ' . str_replace(Environment::getPublicPath() . '/', '', $extendPath) . '
***********************************************************************/
    /**
     * @var int
     */
    protected $otherProperty = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $otherStorage = \'\';


    /**
     * Getter for otherProperty
     *
     * @return int
     */
    public function getOtherProperty()
    {
        return $this->otherProperty;
    }

    /**
     * Setter for otherProperty
     *
     * @param int $otherProperty
     *
     * @return void
     */
    public function setOtherProperty($otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getOtherStorage()
    {
        return $this->otherStorage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $otherStorage
     */
    public function setOtherStorage($otherStorage)
    {
        $this->otherStorage = $otherStorage;
    }

    public function __construct($property = \'\')
    {
        $this->property = $property;
        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->otherStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

}
#';

        $actual = $cacheMock->get($cacheEntryIdentifier);

        $this->assertEquals($expected, $actual);
    }
}
