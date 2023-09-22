<?php

namespace Evoweb\Extender\Tests\Functional;

use Composer\Autoload\ClassLoader;
use Evoweb\Extender\Cache\CacheFactory;
use Evoweb\Extender\Configuration\Register;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Psr\Container\ContainerExceptionInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AbstractTestBase extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/extender',
        'typo3conf/ext/base_extension',
        'typo3conf/ext/extending_extension',
    ];

    public function setUp(): void
    {
        CacheFactory::configureCache();
        parent::setUp();
    }

    protected function getExpected(string $expectedFile, string $basePath = '', string $extendPath = ''): string
    {
        $expectedFile = str_replace(chr(92), '_', $expectedFile) . '.txt';
        $filePath = realpath(__DIR__ . '/../Fixtures/Expected/') . '/' . $expectedFile;

        $searchAndReplace = [
            '###BASE_PATH###' => str_replace(Environment::getPublicPath(), '', $basePath),
            '###EXTEND_PATH###' => str_replace(Environment::getPublicPath(), '', $extendPath),
        ];
        $subject = trim(file_get_contents($filePath));
        return str_replace(array_keys($searchAndReplace), array_values($searchAndReplace), $subject);
    }

    protected function getPhpVersion(): string
    {
        $version = explode('.', PHP_VERSION);
        return $version[0] * 100 + $version[1];
    }

    protected function convertStatementsIntoCode(array $statements): string
    {
        return trim((new PrettyPrinter())->prettyPrint($statements));
    }

    protected function getComposerClassLoader(): ?ClassLoader
    {
        $classLoader = null;
        try {
            $classLoader = GeneralUtility::getContainer()->get(ClassLoader::class);
        } catch (ContainerExceptionInterface $e) {}
        return $classLoader;
    }

    protected function getRegister(): ?Register
    {
        $register = null;
        try {
            $register = GeneralUtility::getContainer()->get(Register::class);
        } catch (ContainerExceptionInterface $e) {}
        return $register;
    }
}
