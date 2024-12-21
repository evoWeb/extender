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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class BlobExtend extends \EvowebTests\BaseExtension\Domain\Model\Blob implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected int $otherProperty = 0;

    public function __construct(string $property = 'b', int $otherProperty = 1)
    {
        parent::__construct($property);
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
