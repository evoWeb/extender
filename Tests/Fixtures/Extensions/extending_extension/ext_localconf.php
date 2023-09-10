<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ExtendingExtension',
    'Test',
    [
        \Fixture\ExtendingExtension\Controller\TestController::class => 'show',
    ]
);
