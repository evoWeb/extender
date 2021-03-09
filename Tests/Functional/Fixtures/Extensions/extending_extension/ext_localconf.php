<?php

$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][
    \Fixture\BaseExtension\Domain\Model\Blob::class
]['extending_extension'] = 'EXT:extending_extension/Classes/Domain/Model/BlobExtend.php';

$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][
    \Fixture\BaseExtension\Domain\Model\BlobWithStorage::class
]['extending_extension'] = 'EXT:extending_extension/Classes/Domain/Model/BlobWithStorageExtend.php';
