<?php

namespace Fixture\ExtendingExtension\Domain\Model;

use Evoweb\Extender\Exception;
use Fixture\BaseExtension\Domain\Model\Blob;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait as T;

class BlobExtend extends Blob implements LoggerAwareInterface
{
    use T;

    protected int $otherProperty = 0;

    public function __construct($property = 'b', $otherProperty = 1)
    {
        parent::__construct();
        $this->otherProperty = $otherProperty;
    }

    public function getOtherProperty(): int
    {
        return $this->otherProperty;
    }

    public function setOtherProperty(int $otherProperty): void
    {
        $this->otherProperty = $otherProperty;
    }
}
