<?php

namespace Fixture\ExtendingExtension\Domain\Model;

class BlobWithStorageExtend extends \Fixture\BaseExtension\Domain\Model\BlobWithStorage
{
    protected int $otherProperty = 0;

    protected \TYPO3\CMS\Extbase\Persistence\ObjectStorage $otherStorage;

    public function __construct()
    {
        $this->otherStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    public function getOtherProperty(): int
    {
        return $this->otherProperty;
    }

    public function setOtherProperty(int $otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }

    public function getOtherStorage(): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        return $this->otherStorage;
    }

    public function setOtherStorage(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $otherStorage)
    {
        $this->otherStorage = $otherStorage;
    }
}
