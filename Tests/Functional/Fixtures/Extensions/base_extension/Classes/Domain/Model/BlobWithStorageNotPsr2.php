<?php
namespace Fixture\BaseExtension\Domain\Model;

class BlobWithStorageNotPsr2 extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $property = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected $storage = '';

    public function __construct() {
        $this->storage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property) {
        $this->property = $property;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage
     */
    public function setStorage($storage) {
        $this->storage = $storage;
    }
}
