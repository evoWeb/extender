<?php

namespace Evoweb\Extender\Tests\Functional12\Utility;

use Fixture\BaseExtension\Domain\Model\AnotherBlob;
use Fixture\BaseExtension\Domain\Model\Blob;
use Fixture\BaseExtension\Domain\Model\BlobWithStorage;
use Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AbstractTestBase extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/extender',
        'vendor/evoweb/base_extension',
        'vendor/evoweb/extending_extension',
    ];

    protected array $cacheConfiguration = [
        'frontend' => PhpFrontend::class,
        'backend' => FileBackend::class,
        'groups' => [
            'all',
            'system',
        ],
        'options' => [
            'defaultLifetime' => 0,
        ],
    ];

    /**
     * Setup some basic values needed in tests
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->configureModelExtending();
    }

    public function getExpected(string $expectedFile, string $basePath, string $extendPath = ''): string
    {
        $expected = file_get_contents(__DIR__ . '/../../Fixtures/Expected/' . $expectedFile . '.txt');
        return str_replace(
            [
                '###BASE_PATH###',
                '###EXTEND_PATH###'
            ],
            [
                str_replace(Environment::getPublicPath() . '/', '', $basePath),
                str_replace(Environment::getPublicPath() . '/', '', $extendPath),
            ],
            $expected
        );
    }

    /**
     * Add cache and extending configuration
     */
    protected function configureModelExtending(): void
    {
        // normally this would be set in ext_localconf
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = $this->cacheConfiguration;

        $extender =& $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'];

        $extender[Blob::class]['extending_extension']
            = 'EXT:extending_extension/Classes/Domain/Model/BlobExtend.php';

        $extender[AnotherBlob::class]['extending_extension']
            = 'EXT:extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $extender[BlobWithStorage::class]['extending_extension']
            = 'EXT:extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $extender[BlobWithStorageAndConstructorArgument::class]['extending_extension']
            = 'EXT:extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';
    }
}
