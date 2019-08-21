<?php
namespace Evoweb\Extender\Tests\Functional\Utility;

use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractTestBase extends \TYPO3\TestingFramework\Core\Functional\FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/extender',
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
        $this->prepareFixtureClassMap();
        $this->activateFixtureExtensions();
    }

    /**
     * Add cache and extending configuration
     */
    protected function configureModelExtending()
    {
        // normally this is set in ext_localconf
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = $this->cacheConfiguration;

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobExtend',
        ];
        $className = \Fixture\BaseExtension\Domain\Model\AnotherBlob::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobWithStorageExtend',
        ];
        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobWithStorageExtend',
        ];
        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobWithStorageExtend',
        ];
    }

    /**
     * Add classmap for fixture files
     */
    protected function prepareFixtureClassMap()
    {
        $composerClassLoader = GeneralUtility::getContainer()->get(\Composer\Autoload\ClassLoader::class);

        $baseFolder = __DIR__ . '/../Fixtures/Extensions/base_extension/Classes/Domain/';
        $extendingFolder = __DIR__ . '/../Fixtures/Extensions/extending_extension/Classes/Extending/';

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        $filePath = realpath($baseFolder . 'Model/Blob.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\BaseExtension\Domain\Model\AnotherBlob::class;
        $filePath = realpath($baseFolder . 'Model/AnotherBlob.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class;
        $filePath = realpath($baseFolder . 'Model/BlobWithStorage.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class;
        $filePath = realpath($baseFolder . 'Model/BlobWithStorageAndConstructorArgument.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\ExtendingExtension\Extending\Model\BlobExtend::class;
        $filePath = realpath($extendingFolder . 'Model/BlobExtend.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\ExtendingExtension\Extending\Model\BlobWithStorageExtend::class;
        $filePath = realpath($extendingFolder . 'Model/BlobWithStorageExtend.php');
        $composerClassLoader->addClassMap([$className => $filePath]);
    }

    /**
     * Add fixture extensions to activated packages
     */
    protected function activateFixtureExtensions()
    {
        $utility = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::class);
        $reflection = new \ReflectionProperty($utility, 'packageManager');
        $reflection->setAccessible(true);
        /** @var \TYPO3\CMS\Core\Package\UnitTestPackageManager $packageManager */
        $packageManager = $reflection->getValue('packageManager');

        $fixtureFolder = __DIR__ . '/../Fixtures/Extensions/';
        $packages = [];
        $packages['base_extension'] = new Package(
            $packageManager,
            'base_extension',
            realpath($fixtureFolder . 'base_extension/') . '/'
        );
        $packages['extending_extension'] = new Package(
            $packageManager,
            'extending_extension',
            realpath($fixtureFolder . 'extending_extension/') . '/'
        );

        $reflection = new \ReflectionProperty(get_class($packageManager), 'activePackages');
        $reflection->setAccessible(true);
        $reflection->setValue($packageManager, $packages);

        $reflection = new \ReflectionProperty(get_class($packageManager), 'packages');
        $reflection->setAccessible(true);
        $reflection->setValue($packageManager, $packages);

        $reflection = new \ReflectionProperty(get_class($packageManager), 'runtimeActivatedPackages');
        $reflection->setAccessible(true);
        $reflection->setValue(
            $packageManager,
            [
                'base_extension' => 1,
                'extending_extension' => 1
            ]
        );
    }
}
