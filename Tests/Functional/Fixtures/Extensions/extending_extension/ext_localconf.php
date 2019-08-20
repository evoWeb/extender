<?php

$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][\Fixture\BaseExtension\Domain\Model\Blob::class] =
    [
        'extending_extension' => 'Model/BlobExtend',
    ];


$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][\Fixture\BaseExtension\Domain\Model\BlobWithStorage::class] =
    [
        'extending_extension' => 'Model/BlobWithStorageExtend',
    ];
