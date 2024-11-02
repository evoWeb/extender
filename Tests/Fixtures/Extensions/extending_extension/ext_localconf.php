<?php

use Fixture\ExtendingExtension\Controller\TestController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
    'ExtendingExtension',
    'Test',
    [
        TestController::class => 'show',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
