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

namespace Evoweb\Extender\Composer\Generator;

use PhpParser\Node\Stmt\Namespace_;

class NamespaceGenerator implements GeneratorInterface
{
    public function generate(array $statements, array $fileSegments): array
    {
        foreach ($fileSegments as $fileSegment) {
            if (!$fileSegment->isBaseClass()) {
                continue;
            }
            $statements[] = new Namespace_($fileSegment->getNamespace());
        }

        return $statements;
    }
}
