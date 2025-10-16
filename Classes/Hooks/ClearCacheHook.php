<?php

namespace Evoweb\Extender\Hooks;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;

class ClearCacheHook
{
    public function __construct(
        protected PhpFrontend $classCache,
        protected CacheManager $cacheManager
    )
    {}

    /**
     * @param array<non-empty-string, string|string[]> $parameters
     */
    public function clearCachePostProc(array $parameters): void
    {
        if (Environment::getContext()->isDevelopment() && ($parameters['cacheCmd'] ?? '') === 'all') {
            $this->classCache->flush();

            if ($this->cacheManager->hasCache('extbase_reflection')) {
                $this->cacheManager->getCache('extbase_reflection')?->flush();
            }
        }
    }
}
