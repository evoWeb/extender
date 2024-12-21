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
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Namespace_;

class ConstantGenerator implements GeneratorInterface
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
            $class->stmts = [...$class->stmts, ...$this->getUniqueClassConstants($fileSegments)];
        }

        return $statements;
    }

    /**
     * @param FileSegments[] $fileSegments
     * @return ClassConst[]
     */
    protected function getUniqueClassConstants(array $fileSegments): array
    {
        $classConstant = [];
        foreach ($fileSegments as $fileSegment) {
            foreach ($fileSegment->getClassConsts() as $currentClassConstant) {
                foreach ($currentClassConstant->consts as $currentConst) {
                    if (isset($classConstant[(string)$currentConst->name])) {
                        continue;
                    }
                    $classConstant[(string)$currentConst->name] = new ClassConst([$currentConst]);
                }
            }
        }
        return array_values($classConstant);
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
