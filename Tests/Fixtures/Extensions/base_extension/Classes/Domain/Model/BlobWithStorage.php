<?php

namespace Fixture\BaseExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

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
