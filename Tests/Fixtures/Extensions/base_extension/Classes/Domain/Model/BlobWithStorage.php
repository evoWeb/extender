<?php

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

namespace Fixture\BaseExtension\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class BlobWithStorage extends AbstractEntity
{
    protected string $property = '';

    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $storage;

    public function __construct()
    {
        $this->storage = new ObjectStorage();
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    public function getStorage(): ObjectStorage
    {
        return $this->storage;
    }

    public function setStorage(ObjectStorage $storage): void
    {
        $this->storage = $storage;
    }
}
