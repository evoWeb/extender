<?php

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['base_extension']['extender'][\Fixture\BaseExtension\Domain\Model\Blob::class] =
    [
        'extending_extension' => 'Model/BlobExtend',
    ];
