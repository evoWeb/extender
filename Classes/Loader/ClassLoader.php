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

namespace Evoweb\Extender\Loader;

use Evoweb\Extender\Cache\ClassCacheManager;
use Evoweb\Extender\Configuration\Register;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\SingletonInterface;

class ClassLoader implements SingletonInterface
{
    /**
     * @var FrontendInterface|PhpFrontend
     */
    protected FrontendInterface $classCache;

    protected ClassCacheManager $classCacheManager;

    protected Register $register;

    public function __construct(
        FrontendInterface $classCache,
        ClassCacheManager $classCacheManager,
        Register $register
    ) {
        $this->classCache = $classCache;
        $this->classCacheManager = $classCacheManager;
        $this->register = $register;
    }

    /**
     * Loads php files containing classes or interfaces part of the
     * classes directory of an extension.
     */
    public function loadClass(string $className): bool
    {
        $className = ltrim($className, '\\');

        $return = false;
        if ($this->isValidClassName($className)) {
            $cacheEntryIdentifier = str_replace('\\', '_', $className);

            if (!$this->classCache->has($cacheEntryIdentifier)) {
                $this->classCacheManager->build($cacheEntryIdentifier, $className);
            }

            $this->classCache->requireOnce($cacheEntryIdentifier);
            $return = true;
        }

        return $return;
    }

    protected function isValidClassName(string $className): bool
    {
        return $this->register->hasBaseClassName($className);
    }
}
