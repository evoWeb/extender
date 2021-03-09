<?php

namespace Fixture\BaseExtension\Domain\Model;

class Blob extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $property = '';

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property)
    {
        $this->property = $property;
    }
}
