<?php

namespace Evoweb\Extender\Tests\Functional\Utility;

use Composer\Autoload\ClassLoader;
use Evoweb\Extender\Cache\CacheManager;
use Evoweb\Extender\Configuration\Register;
use TYPO3\CMS\Core\Cache\Backend\AbstractBackend;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AbstractTestBase extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/extender',
        'typo3conf/ext/base_extension',
        'typo3conf/ext/extending_extension',
    ];

    protected array $cacheConfiguration = [
        'frontend' => PhpFrontend::class,
        'backend' => FileBackend::class,
        'groups' => [
            'all',
            'system',
        ],
        'options' => [
            'defaultLifetime' => AbstractBackend::UNLIMITED_LIFETIME,
        ],
    ];

    /**
     * Setup some basic values needed in tests
     */
    public function setUp(): void
    {
        CacheManager::configureCache();
        parent::setUp();
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

    protected function getComposerClassLoader(): ClassLoader
    {
        return GeneralUtility::getContainer()->get(ClassLoader::class);
    }

    protected function getRegister(): Register
    {
        return GeneralUtility::getContainer()->get(Register::class);
    }
}
