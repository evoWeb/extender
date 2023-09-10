<?php

defined('TYPO3') or die();

use Evoweb\Extender\Cache\CacheManager;
use Evoweb\Extender\Hooks\DataHandlerClearCachePostProcHook;

call_user_func(function () {
    CacheManager::configureCache();

    // Configure clear cache post-processing for extended domain model
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
        DataHandlerClearCachePostProcHook::class . '->clear';
});
