<?php
namespace Fixture\ExtendingExtension\Extending\Model;

class BlobWithStorageExtend extends \Fixture\BaseExtension\Domain\Model\BlobWithStorage
{
    /**
     * @var int
     */
    protected $otherProperty = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $otherStorage = '';

    public function __construct()
    {
        $this->otherStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Getter for otherProperty
     *
     * @return int
     */
    public function getOtherProperty()
    {
        return $this->otherProperty;
    }

    /**
     * Setter for otherProperty
     *
     * @param int $otherProperty
     *
     * @return void
     */
    public function setOtherProperty($otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getOtherStorage()
    {
        return $this->otherStorage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $otherStorage
     */
    public function setOtherStorage($otherStorage)
    {
        $this->otherStorage = $otherStorage;
    }
}
