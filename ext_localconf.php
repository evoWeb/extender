<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {
    // Register extender cache
    // needs to stay above registerAutoloader to always have settings before using the cache
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
        'groups' => [
            'all',
            'system',
        ],
        'options' => [
            'defaultLifetime' => 0,
        ],
    ];


    if (class_exists(\Evoweb\Extender\Utility\ClassLoader::class)) {
        \Evoweb\Extender\Utility\ClassLoader::registerAutoloader();
    }


    // Configure clear cache post processing for extended domain model
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
        Evoweb\Extender\Utility\ClassCacheManager::class . '->reBuild';
});
