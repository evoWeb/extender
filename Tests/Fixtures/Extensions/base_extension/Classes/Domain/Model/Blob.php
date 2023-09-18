<?php

namespace Fixture\BaseExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Blob extends AbstractEntity
{
    protected string $property = '';

    public function __construct($property = 'a')
    {
        $this->property = $property;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    protected string $secondProperty = '';

    protected string $thirdProperty = '';

    public function getSecondProperty(): string
    {
        return $this->secondProperty;
    }

    public function setSecondProperty(string $secondProperty): void
    {
        $this->secondProperty = $secondProperty;
    }

    public function getThirdProperty(): string
    {
        return $this->thirdProperty;
    }

    public function setThirdProperty(string $thirdProperty): void
    {
        $this->thirdProperty = $thirdProperty;
    }
}
