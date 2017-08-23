<?php
namespace Evoweb\Extender\Tests\Unit7\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

class ClassCacheManagerTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function parseSingleFile()
    {
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['parseSingleFile'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(__DIR__ . '/../../Unit/Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php');

        $expected = '/***********************************************************************
 * this is partial from:
 *  ' . str_replace(PATH_site, '', $filePath) . '
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
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['parseSingleFile', '_get'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(
            __DIR__ . '/../../Unit/Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorage.php'
        );

        $expected = '/***********************************************************************
 * this is partial from:
 *  ' . str_replace(PATH_site, '', $filePath) . '
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

        /** @var \Evoweb\Extender\Utility\ClassCacheManager|AccessibleObjectInterface $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['parseSingleFile', '_get'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(
            __DIR__ . '/../../Unit/Fixtures/Extensions/base_extension/Classes/Domain/Model/BlobWithStorageNotPsr2.php'
        );

        $expected = '/***********************************************************************
 * this is partial from:
 *  ' . str_replace(PATH_site, '', $filePath) . '
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
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['changeCode'])
            ->setConstructorArgs([$cacheMock])
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
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['getPartialInfo'])
            ->setConstructorArgs([$cacheMock])
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
        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->getAccessibleMock(
            \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
            [],
            ['extender', $cacheBackend]
        );

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['closeClassDefinition'])
            ->setConstructorArgs([$cacheMock])
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
        $this->prepareFixtureClassMap();
        $this->activateFixtureExtensions();

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobExtend',
        ];

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $fixtureFolder = __DIR__ . '/../../Unit/Fixtures/Extensions/';
        $basePath = realpath($fixtureFolder . 'base_extension/Classes/Domain/Model/Blob.php');
        $extendPath = realpath($fixtureFolder . 'extending_extension/Classes/Extending/Model/BlobExtend.php');

        $expected = '<?php
/***********************************************************************
 * this is partial from:
 *  ' . str_replace(PATH_site, '', $basePath) . '
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
 *  ' . str_replace(PATH_site, '', $extendPath) . '
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
        $this->prepareFixtureClassMap();
        $this->activateFixtureExtensions();

        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobWithStorageExtend',
        ];

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $fixtureFolder = __DIR__ . '/../../Unit/Fixtures/Extensions/';
        $basePath = realpath($fixtureFolder . 'base_extension/Classes/Domain/Model/BlobWithStorage.php');
        $extendPath = realpath(
            $fixtureFolder . 'extending_extension/Classes/Extending/Model/BlobWithStorageExtend.php'
        );

        $expected = '<?php
/***********************************************************************
 * this is partial from:
 *  ' . str_replace(PATH_site, '', $basePath) . '
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
 *  ' . str_replace(PATH_site, '', $extendPath) . '
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
    public function reBuildWithStorageAndConstructorArgument()
    {
        $this->prepareFixtureClassMap();
        $this->activateFixtureExtensions();

        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobWithStorageExtend',
        ];

        $cacheBackend = new \TYPO3\CMS\Core\Cache\Backend\FileBackend('production');
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = new \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend('extender', $cacheBackend);

        $subject = new \Evoweb\Extender\Utility\ClassCacheManager($cacheMock);
        $subject->reBuild();

        $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase('base_extension') . '_' .
            str_replace('\\', '_', $className);

        $fixtureFolder = __DIR__ . '/../../Unit/Fixtures/Extensions/';
        $basePath = realpath($fixtureFolder . 'base_extension/Classes/Domain/Model/BlobWithStorageAndConstructorArgument.php');
        $extendPath = realpath(
            $fixtureFolder . 'extending_extension/Classes/Extending/Model/BlobWithStorageExtend.php'
        );

        $expected = '<?php
/***********************************************************************
 * this is partial from:
 *  ' . str_replace(PATH_site, '', $basePath) . '
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
 *  ' . str_replace(PATH_site, '', $extendPath) . '
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
