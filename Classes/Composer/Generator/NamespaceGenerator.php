<?php

declare(strict_types=1);

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

namespace Evoweb\Extender\Composer\Generator;

use Evoweb\Extender\Parser\FileSegments;
use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;

class NamespaceGenerator implements GeneratorInterface
{
    /**
     * @param Node[] $statements
     * @param FileSegments[] $fileSegments
     * @return Node[]
     */
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
