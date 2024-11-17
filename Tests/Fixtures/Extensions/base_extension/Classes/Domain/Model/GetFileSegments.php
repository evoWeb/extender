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

class GetFileSegments
{
    use \EvowebTests\BaseExtension\Traits\TestTrait;

    protected string $testProperty = '';

    public function __construct() {}

    public function getTestProperty(): string
    {
        return $this->testProperty;
    }
}
