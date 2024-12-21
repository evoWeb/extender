<?php

declare(strict_types=1);

/*
 * This file is part of the "extender" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Configuration;

class ClassRegister
{
    /**
     * @param array<string, string[]> $extendedClasses
     */
    public function __construct(protected array $extendedClasses = []) {}

    public function hasBaseClassName(string $className): bool
    {
        return isset($this->extendedClasses[$className]);
    }

    /**
     * @return string[]
     */
    public function getExtendingClasses(string $className): array
    {
        return is_array($this->extendedClasses[$className] ?? false)
            ? $this->extendedClasses[$className]
            : [];
    }
}
