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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;

class TraitGenerator implements GeneratorInterface
{
    /**
     * @param Node[] $statements
     * @param FileSegments[] $fileSegments
     * @return Node[]
     */
    public function generate(array $statements, array $fileSegments): array
    {
        $namespace = $this->getNamespace($statements);
        $class = $this->getClass($namespace);

        if ($class) {
            $class->stmts = [...$class->stmts, ...$this->getUniqueTraits($fileSegments)];
        }

        return $statements;
    }

    /**
     * @param FileSegments[] $fileSegments
     * @return TraitUse[]
     */
    protected function getUniqueTraits(array $fileSegments): array
    {
        $traits = [];
        foreach ($fileSegments as $fileSegment) {
            foreach ($fileSegment->getTraits() as $currentTraits) {
                foreach ($currentTraits->traits as $currentTrait) {
                    if (isset($traits[(string)$currentTrait])) {
                        continue;
                    }
                    $traits[(string)$currentTrait] = new TraitUse([$currentTrait]);
                }
            }
        }
        return array_values($traits);
    }

    /**
     * @param Node[] $statements
     */
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

    protected function getClass(Namespace_ $namespace): ?Class_
    {
        /** @var ?Class_ $class */
        $class = null;
        foreach ($namespace->stmts as $node) {
            if ($node instanceof Class_) {
                $class = $node;
                break;
            }
        }
        return $class;
    }
}