<?php

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace EvowebTests\BaseExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Blob extends AbstractEntity
{
    protected string $property = '';

    public function __construct(string $property = 'a')
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
