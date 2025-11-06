<?php
namespace Evoweb\Extender\Hooks;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Event\CacheFlushEvent;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\BootService;
use TYPO3\CMS\Core\Core\Environment;

class ClearCacheHook
{
    public function __construct(
        protected PhpFrontend $classCache,
        protected readonly BootService $bootService
    )
    {}

    /**
     * @param array<non-empty-string, string|string[]> $parameters
     */
    public function clearCachePostProc(array $parameters): void
    {
        if (Environment::getContext()->isDevelopment() && ($parameters['cacheCmd'] ?? '') === 'all') {
            $this->classCache->flush();
        }

        $container = $this->bootService->getContainer(true);
        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        $container = $this->bootService->getContainer(true);
        $groups = $container->get(CacheManager::class)->getCacheGroups();

        $event = new CacheFlushEvent($groups);
        $eventDispatcher->dispatch($event);
    }
}
