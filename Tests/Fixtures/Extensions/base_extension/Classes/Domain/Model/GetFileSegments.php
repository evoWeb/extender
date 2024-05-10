<?php

namespace Fixture\BaseExtension\Domain\Model;

use Fixture\BaseExtension\Domain\Test;

class GetFileSegments
{
    use Fixture\BaseExtension\TestTrait;

    protected string $testProperty = '';

    public function __construct()
    {
    }

    public function getTestProperty(): string
    {
        return $this->testProperty;
    }
}
