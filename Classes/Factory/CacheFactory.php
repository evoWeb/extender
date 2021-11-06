<?php

declare(strict_types=1);

namespace Evoweb\Extender\Factory;

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

use TYPO3\CMS\Core\Cache\Backend\AbstractBackend;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Bootstrap;

class CacheFactory
{
    /**
     * @var array
     */
    public static $configuration = [
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

    public static function createCache($identifier): FrontendInterface
    {
        if (!$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender']) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = self::$configuration;
        }

        return Bootstrap::createCache($identifier);
    }
}
