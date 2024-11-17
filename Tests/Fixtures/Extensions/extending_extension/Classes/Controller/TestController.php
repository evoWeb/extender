<?php

declare(strict_types=1);

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace EvowebTests\ExtendingExtension\Controller;

use EvowebTests\BaseExtension\Domain\Model\Blob;
use EvowebTests\BaseExtension\Domain\Model\BlobWithStorage;
use EvowebTests\ExtendingExtension\Domain\Model\BlobExtend;
use EvowebTests\ExtendingExtension\Domain\Model\BlobWithStorageExtend;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TestController extends ActionController
{
    public function showAction(): ResponseInterface
    {
        /** @var BlobExtend $blob */
        $blob = GeneralUtility::makeInstance(Blob::class);
        $blob->setOtherProperty(1);
        $extend1 = get_class($blob) . ' ' . $blob->getOtherProperty();

        /** @var BlobWithStorageExtend $blobWithStorage */
        $blobWithStorage = GeneralUtility::makeInstance(BlobWithStorage::class);
        $blobWithStorage->setOtherProperty(1);
        $extend2 = get_class($blobWithStorage) . ' ' . $blobWithStorage->getOtherProperty();

        return $this->htmlResponse($extend1 . ' ' . $extend2);
    }
}
