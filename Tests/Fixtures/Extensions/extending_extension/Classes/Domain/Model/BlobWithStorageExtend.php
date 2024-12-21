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

namespace EvowebTests\ExtendingExtension\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class BlobWithStorageExtend extends \EvowebTests\BaseExtension\Domain\Model\BlobWithStorage
{
    protected int $otherProperty = 0;

    /**
     * @var ObjectStorage<Category>
     */
    protected ObjectStorage $otherStorage;

    public function __construct()
    {
        parent::__construct();
        $this->otherStorage = new ObjectStorage();
    }

    public function getOtherProperty(): int
    {
        return $this->otherProperty;
    }

    public function setOtherProperty(int $otherProperty): void
    {
        $this->otherProperty = $otherProperty;
    }

    /**
     * @return ObjectStorage<Category>
     */
    public function getOtherStorage(): ObjectStorage
    {
        return $this->otherStorage;
    }

    /**
     * @param ObjectStorage<Category> $otherStorage
     */
    public function setOtherStorage(ObjectStorage $otherStorage): void
    {
        $this->otherStorage = $otherStorage;
    }
}
