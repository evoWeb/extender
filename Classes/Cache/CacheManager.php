<?php

declare(strict_types=1);

/*
 * This file is part of the "extender" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Cache;

use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Cache\Backend\AbstractBackend;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;

class CacheManager
{
    public function createCache(string $identifier): FrontendInterface
    {
        self::configureCache();
        return Bootstrap::createCache($identifier);
    }

    public static function configureCache(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = [
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
    }
}
