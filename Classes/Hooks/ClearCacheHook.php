<?php

declare(strict_types=1);

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Hooks;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;

#[Autoconfigure(public: true)]
class ClearCacheHook
{
    public function __construct(
        #[Autowire(service: 'cache.extender')]
        protected PhpFrontend $classCache
    ) {
    }

    /**
     * @param array<non-empty-string, string|string[]> $parameters
     */
    public function clearCachePostProc(array $parameters): void
    {
        if (Environment::getContext()->isDevelopment() && ($parameters['cacheCmd'] ?? '') === 'all') {
            $this->classCache->flush();
        }
    }
}
