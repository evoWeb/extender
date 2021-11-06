<?php

defined('TYPO3_MODE') || die();

call_user_func(function () {
    // Register extender cache
    // needs to stay above registerAutoloader to always have settings before using the cache
    if (!$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender']) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] =
            \Evoweb\Extender\Factory\CacheFactory::$configuration;
    }

    $event = new \Evoweb\Extender\Utility\Event\ClassLoaderEvent();
    /** @var \TYPO3\CMS\Core\EventDispatcher\EventDispatcher $eventDispatcher */
    $eventDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::getContainer()
        ->get(Psr\EventDispatcher\EventDispatcherInterface::class);
    $eventDispatcher->dispatch($event);

    // Configure clear cache post processing for extended domain model
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
        \Evoweb\Extender\Utility\ClassCacheManager::class . '->reBuild';
});
