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

use Evoweb\Extender\Parser\FileSegments;
use PhpParser\Node\Name;
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
                if ($fileSegment->isBaseClass()) {
                    continue;
                }
                $class->implements = $this->getUniqueImplements($fileSegments);
            }
            $namespace->stmts[] = $class;
        }

        return $statements;
    }

    protected function getUniqueImplements(array $fileSegments): array
    {
        $implements = [];
        /** @var FileSegments $fileSegment */
        foreach ($fileSegments as $fileSegment) {
            /** @var Name $currentImplement */
            foreach ($fileSegment->getClass()->implements as $currentImplement) {
                if (isset($implements[(string)$currentImplement])) {
                    continue;
                }
                $implements[(string)$currentImplement] = $currentImplement;
            }
        }
        return array_values($implements);
    }

    protected function getNamespace(array $statements): ?Namespace_
    {
        $namespace = null;
        foreach ($statements as $statement) {
            if ($statement instanceof Namespace_) {
                $namespace = $statement;
                break;
            }
        }
        return $namespace;
    }

    protected function getClass(array $fileSegments): ?Class_
    {
        $class = null;
        /** @var FileSegments $fileSegment */
        foreach ($fileSegments as $fileSegment) {
            if ($fileSegment->isBaseClass() && $fileSegment->getClass()) {
                $class = $fileSegment->getClass();
                break;
            }
        }
        return $class;
    }
}
