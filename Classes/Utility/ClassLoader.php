<?php

declare(strict_types=1);

namespace Evoweb\Extender\Utility;

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

use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassLoader implements SingletonInterface
{
    protected PhpFrontend $classCache;

    protected ClassCacheManager $classCacheManager;

    /**
     * Known classnames that cause problems and can not be extended
     *
     * @var array
     */
    protected array $excludedClassNames = [
        'Symfony\Polyfill\Mbstring\Mbstring'
    ];

    public function __construct(PhpFrontend $classCache, ClassCacheManager $classCacheManager)
    {
        $this->classCache = $classCache;
        $this->classCacheManager = $classCacheManager;
    }

    /**
     * Loads php files containing classes or interfaces part of the
     * classes directory of an extension.
     *
     * @param string $className Name of the class/interface to load
     *
     * @return bool
     */
    public function loadClass(string $className): bool
    {
        $className = ltrim($className, '\\');
        $extensionKey = $this->getExtensionKeyFromNamespace($className);

        $return = false;
        if (!$this->isExcludedClassName($className) && $this->isValidClassName($className, $extensionKey)) {
            $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase($extensionKey)
                . '_' . str_replace('\\', '_', $className);

            if (!$this->classCache->has($cacheEntryIdentifier)) {
                $this->classCacheManager->reBuild();
            }
            $this->classCache->requireOnce($cacheEntryIdentifier);
            $return = true;
        }

        return $return;
    }

    protected function isExcludedClassName(string $className): bool
    {
        $result = false;

        if (in_array($className, $this->excludedClassNames)) {
            $result = true;
        }

        return $result;
    }

    protected function getExtensionKeyFromNamespace(string $className): string
    {
        $extensionKey = '';

        if (strpos($className, '\\') !== false) {
            $namespaceParts = GeneralUtility::trimExplode(
                '\\',
                $className,
                0,
                (substr($className, 0, 9) === 'TYPO3\\CMS' ? 4 : 3)
            );
            array_pop($namespaceParts);
            $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored(array_pop($namespaceParts));
        }

        return $extensionKey;
    }

    protected function isValidClassName(string $className, string $extensionKey): bool
    {
        $oldClassnamePart = substr(strtolower($className), 0, 5);

        $extensionConfiguration = [];
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey])) {
            $extensionConfiguration = (array)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey];
        }

        return (bool)preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9\\\\_\x7f-\xff]*$/', $className)
            && (
                strpos($oldClassnamePart, 'tx_') === false
                && strpos($oldClassnamePart, 'ux_') === false
                && strpos($oldClassnamePart, 'user_') === false
            )
            && (
                isset($extensionConfiguration['extender'])
                && is_array($extensionConfiguration['extender'])
                && isset($extensionConfiguration['extender'][$className])
            );
    }
}
