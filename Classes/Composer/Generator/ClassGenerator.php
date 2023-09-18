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

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;

class ClassGenerator implements GeneratorInterface
{
    public function generate(array $statements, array $fileSegments): array
    {
        $namespace = $this->getNamespace($statements);
        $class = $this->getClass($fileSegments);

        if ($class) {
            foreach ($fileSegments as $fileSegment) {
                if ($fileSegment['baseClass']) {
                    continue;
                }
                $class->implements = [...$class->implements, ...$fileSegment['class']->implements];
            }
            $namespace->stmts[] = $class;
        }

        return $statements;
    }

    protected function getNamespace(array $statements): Namespace_
    {
        return $statements[0];
    }

    protected function getClass(array $fileSegments): ?Class_
    {
        $class = null;
        foreach ($fileSegments as $fileSegment) {
            if ($fileSegment['baseClass'] && $fileSegment['class']) {
                $class = $fileSegment['class'];
            }
        }
        return $class;
    }
}
