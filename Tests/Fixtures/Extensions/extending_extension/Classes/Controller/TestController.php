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

namespace Fixture\ExtendingExtension\Controller;

use Fixture\BaseExtension\Domain\Model\Blob;
use Fixture\BaseExtension\Domain\Model\BlobWithStorage;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TestController extends ActionController
{
    public function showAction(): ResponseInterface
    {
        $extend1 = get_class(GeneralUtility::makeInstance(Blob::class));
        $extend2 = get_class(GeneralUtility::makeInstance(BlobWithStorage::class));
        return $this->htmlResponse($extend1 . ' ' . $extend2);
    }
}
