<?php

namespace Fixture\BaseExtension\Domain\Model;

class BlobWithStorageNotPsr2 extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $property = '';

    protected \TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage;

    public function __construct()
    {
        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property)
    {
        $this->property = $property;
    }

    public function getStorage(): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        return $this->storage;
    }

    public function setStorage(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage)
    {
        $this->storage = $storage;
    }
}
