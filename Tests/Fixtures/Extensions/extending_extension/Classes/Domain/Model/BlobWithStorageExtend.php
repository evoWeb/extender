<?php

namespace Fixture\ExtendingExtension\Domain\Model;

use Fixture\BaseExtension\Domain\Model\BlobWithStorage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class BlobWithStorageExtend extends BlobWithStorage
{
    protected int $otherProperty = 0;

    protected ObjectStorage $otherStorage;

    public function __construct()
    {
        parent::__construct();
        $this->otherStorage = new ObjectStorage();
    }

    public function getOtherProperty(): int
    {
        return $this->otherProperty;
    }

    public function setOtherProperty(int $otherProperty): void
    {
        $this->otherProperty = $otherProperty;
    }

    public function getOtherStorage(): ObjectStorage
    {
        return $this->otherStorage;
    }

    public function setOtherStorage(ObjectStorage $otherStorage): void
    {
        $this->otherStorage = $otherStorage;
    }
}
