<?php
defined('TYPO3_MODE') or die();


// Register extender cache
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['extender'] = array(
    'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\PhpFrontend',
    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\FileBackend',
    'groups' => array(
        'all',
        'system',
    ),
    'options' => array(
        'defaultLifetime' => 0,
    ),
);


\Evoweb\Extender\Utility\ClassLoader::registerAutoloader();


// Configure clear cache post processing for extended domain model
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
    'EXT:extender/Classes/Utility/ClassCacheManager.php:Evoweb\Extender\Utility\ClassCacheManager->reBuild';
