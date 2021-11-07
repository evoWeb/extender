<?php

defined('TYPO3') or die();

call_user_func(function () {
    // Configure clear cache post processing for extended domain model
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
        \Evoweb\Extender\Utility\ClassCacheManager::class . '->reBuild';
});
