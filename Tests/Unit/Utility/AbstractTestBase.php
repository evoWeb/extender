<?php
namespace Evoweb\Extender\Tests\Unit\Utility;

use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Package\Package;

class AbstractTestBase extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
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
    public function setUp()
    {
        // normaly this is set in ext_localconf
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = $this->cacheConfiguration;

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['base_extension']['extender'][$className] = [
            'extending_extension' => 'Model/BlobExtend',
        ];
        $classNameWithStorage = \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['base_extension']['extender'][$classNameWithStorage] = [
            'extending_extension' => 'Model/BlobWithStorageExtend',
        ];
        $classNameWithStorageAndConstructorArgument = \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['base_extension']['extender'][$classNameWithStorageAndConstructorArgument] = [
            'extending_extension' => 'Model/BlobWithStorageExtend',
        ];
    }

    /**
     * Add classmap for fixture files
     *
     * @return void
     */
    protected function prepareFixtureClassMap()
    {
        /** @var \Composer\Autoload\ClassLoader $composerClassLoader */
        $composerClassLoader = \TYPO3\CMS\Core\Core\Bootstrap::getInstance()
            ->getEarlyInstance(\Composer\Autoload\ClassLoader::class);

        $fixtureFolder = __DIR__ . '/../Fixtures/Extensions/';

        $className = \Fixture\BaseExtension\Domain\Model\Blob::class;
        $filePath = realpath($fixtureFolder . 'base_extension/Classes/Domain/Model/Blob.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\ExtendingExtension\Extending\Model\BlobExtend::class;
        $filePath = realpath($fixtureFolder . 'extending_extension/Classes/Extending/Model/BlobExtend.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class;
        $filePath = realpath($fixtureFolder . 'base_extension/Classes/Domain/Model/BlobWithStorage.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\BaseExtension\Domain\Model\BlobWithStorageAndConstructorArgument::class;
        $filePath = realpath($fixtureFolder . 'base_extension/Classes/Domain/Model/BlobWithStorageAndConstructorArgument.php');
        $composerClassLoader->addClassMap([$className => $filePath]);

        $className = \Fixture\ExtendingExtension\Extending\Model\BlobWithStorageExtend::class;
        $filePath = realpath($fixtureFolder . 'extending_extension/Classes/Extending/Model/BlobWithStorageExtend.php');
        $composerClassLoader->addClassMap([$className => $filePath]);
    }

    /**
     * Add fixture extenions to activated packages
     *
     * @return void
     */
    protected function activateFixtureExtensions()
    {
        /** @var \TYPO3\CMS\Core\Package\PackageManager $packageManager */
        $packageManager = Bootstrap::getInstance()->getEarlyInstance(\TYPO3\CMS\Core\Package\PackageManager::class);

        $fixtureFolder = __DIR__ . '/../Fixtures/Extensions/';

        $reflection = new \ReflectionProperty(get_class($packageManager), 'activePackages');
        $reflection->setAccessible(true);
        $packages = $reflection->getValue($packageManager);
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
