<?php

namespace Evoweb\TestNamespace;

use Evoweb\Domain\Model\Test;

class GetFileSegments
{
    use Evoweb\TestTrait;

    protected string $testProperty = '';

    public function __construct()
    {
    }

    public function getTestProperty(): string
    {
        return $this->testProperty;
    }
}
