<?php
namespace Evoweb\Extender\Tests\Unit7\Utility;

use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Package\Package;

class AbstractTestBase extends \TYPO3\CMS\Core\Tests\UnitTestCase
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

        $fixtureFolder = __DIR__ . '/../../Unit/Fixtures/Extensions/';

        $className = 'Fixture\\BaseExtension\\';
        $classPath = realpath($fixtureFolder . 'base_extension/Classes/');
        $composerClassLoader->addPsr4($className, [$classPath]);

        $className = 'Fixture\\ExtendingExtension\\';
        $classPath = realpath($fixtureFolder . 'extending_extension/Classes/');
        $composerClassLoader->addPsr4($className, [$classPath]);
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

        $fixtureFolder = __DIR__ . '/../../Unit/Fixtures/Extensions/';

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
