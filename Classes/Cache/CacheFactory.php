<?php

declare(strict_types=1);

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

namespace Evoweb\Extender\Cache;

use Evoweb\Extender\Hooks\ClearCacheHook;
use TYPO3\CMS\Core\Cache\Backend\AbstractBackend;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Bootstrap;

class CacheFactory
{
    /**
     * @var array<string, string|string[]|array<string, string>>
     */
    protected static array $configuration = [
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

    public function createCache(string $identifier): ?FrontendInterface
    {
        self::addClassCacheConfigToGlobalTypo3ConfVars();
        try {
            $cache = Bootstrap::createCache($identifier);
        } catch (\Exception) {
            $cache = null;
        }
        return $cache;
    }

    public static function addClassCacheConfigToGlobalTypo3ConfVars(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = static::$configuration;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['extender'] =
            ClearCacheHook::class . '->clearCachePostProc';
    }
}
