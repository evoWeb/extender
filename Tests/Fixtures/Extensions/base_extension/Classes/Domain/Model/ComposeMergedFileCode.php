<?php

namespace Evoweb\TestNamespace;

use Evoweb\Domain\Model\Test;

class ComposeMergedFileCode
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
