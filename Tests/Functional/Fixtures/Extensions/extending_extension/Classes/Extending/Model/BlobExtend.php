<?php
namespace Fixture\ExtendingExtension\Extending\Model;

class BlobExtend extends \Fixture\BaseExtension\Domain\Model\Blob
{
    /**
     * @var int
     */
    protected $otherProperty = 0;

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
}
