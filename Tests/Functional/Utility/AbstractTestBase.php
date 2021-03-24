<?php

namespace Evoweb\Extender\Tests\Functional\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractTestBase extends \TYPO3\TestingFramework\Core\Functional\FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/extender',
        'typo3conf/ext/base_extension',
        'typo3conf/ext/extending_extension',
    ];

    /**
     * @var array
     */
    protected $cacheConfiguration = [
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

    /**
     * Add cache and extending configuration
     */
    protected function configureModelExtending()
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
