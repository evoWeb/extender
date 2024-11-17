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

namespace Evoweb\Extender\Loader;

use Evoweb\Extender\Cache\ClassCacheManager;
use Evoweb\Extender\Configuration\ClassRegister;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\SingletonInterface;

class ClassLoader implements SingletonInterface
{
    public function __construct(
        protected PhpFrontend $classCache,
        protected ClassCacheManager $classCacheManager,
        protected ClassRegister $classRegister
    ) {}

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
        return $this->classRegister->hasBaseClassName($className);
    }
}
