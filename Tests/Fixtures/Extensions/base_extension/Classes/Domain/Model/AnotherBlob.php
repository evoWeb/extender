<?php

namespace Fixture\BaseExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class AnotherBlob extends AbstractEntity
{
    protected string $property = '';

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }
}
