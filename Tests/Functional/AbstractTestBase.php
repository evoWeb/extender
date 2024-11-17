<?php

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Tests\Functional;

use Composer\Autoload\ClassLoader;
use Evoweb\Extender\Cache\CacheFactory;
use Evoweb\Extender\Configuration\ClassRegister;
use Evoweb\Extender\Parser\FileSegments;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Psr\Container\ContainerExceptionInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AbstractTestBase extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/extender',
        'typo3conf/ext/base_extension',
        'typo3conf/ext/extending_extension',
    ];

    public function setUp(): void
    {
        CacheFactory::addClassCacheConfigToGlobalTypo3ConfVars();
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
        return (string)((int)$version[0] * 100 + (int)$version[1]);
    }

    /**
     * @param Node[] $statements
     */
    protected function convertStatementsIntoCode(array $statements): string
    {
        return trim((new PrettyPrinter())->prettyPrint($statements));
    }

    protected function getClassLoader(): ?ClassLoader
    {
        $classLoader = null;
        try {
            $classLoader = GeneralUtility::getContainer()->get(ClassLoader::class);
        } catch (ContainerExceptionInterface) {
        }
        return $classLoader;
    }

    protected function getClassRegister(): ?ClassRegister
    {
        $classRegister = null;
        try {
            $classRegister = GeneralUtility::getContainer()->get(ClassRegister::class);
        } catch (ContainerExceptionInterface) {
        }
        return $classRegister;
    }
}
