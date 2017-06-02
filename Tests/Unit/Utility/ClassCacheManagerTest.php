<?php
namespace Evoweb\Extender\Tests\Unit\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassCacheManagerTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function parseSingleFile()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['parseSingleFile'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $filePath = realpath(__DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php');

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
    public function changeCode()
    {
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

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
        /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cacheMock */
        $cacheMock = $this->createMock(\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class);

        /** @var \Evoweb\Extender\Utility\ClassCacheManager $subject */
        $subject = $this->getMockBuilder($this->buildAccessibleProxy(\Evoweb\Extender\Utility\ClassCacheManager::class))
            ->setMethods(['getPartialInfo'])
            ->setConstructorArgs([$cacheMock])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $expected = '/***********************************************************************' . chr(10)
            . ' * this is partial from:' . chr(10) . ' *  --' . chr(10)
            . '***********************************************************************/' . chr(10) . '    ';

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

        $basePath = realpath(__DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php');
        $extendPath = realpath(
            __DIR__ . '/../Fixtures/Extensions/extending_extension/Classes/Extending/Model/BlobExtend.php'
        );

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
}
