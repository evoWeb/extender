<?php

namespace Evoweb\Extender\Hooks;

use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;

class ClearCacheHook
{
    public function __construct(protected PhpFrontend $classCache)
    {
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
