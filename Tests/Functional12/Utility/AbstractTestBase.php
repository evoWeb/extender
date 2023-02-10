<?php

namespace Evoweb\Extender\Tests\Functional12\Utility;

use TYPO3\CMS\Core\Core\Environment;

class AbstractTestBase extends \TYPO3\TestingFramework\Core\Functional\FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/extender',
        'vendor/evoweb/base-extension',
        'vendor/evoweb/extending-extension',
    ];

    protected array $cacheConfiguration = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
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

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][
            \Fixture\BaseExtension\Domain\Model\Blob::class
        ]['extending_extension'] = 'EXT:extending_extension/Classes/Domain/Model/BlobExtend.php';

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][
            \Fixture\BaseExtension\Domain\Model\AnotherBlob::class
        ]['extending_extension'] = 'EXT:extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][
            \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class
        ]['extending_extension'] = 'EXT:extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][
            \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class
        ]['extending_extension'] = 'EXT:extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';
    }
}
