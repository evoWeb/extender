<?php

namespace Fixture\ExtendingExtension\Domain\Model;

class BlobExtend extends \Fixture\BaseExtension\Domain\Model\Blob
{
    protected int $otherProperty = 0;

    public function getOtherProperty(): int
    {
        return $this->otherProperty;
    }

    public function setOtherProperty(int $otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }
}
