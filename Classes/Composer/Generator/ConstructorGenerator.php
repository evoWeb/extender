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
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;

class ConstructorGenerator implements GeneratorInterface
{
    public function generate(array $statements, array $fileSegments): array
    {
        $namespace = $this->getNamespace($statements);
        $class = $this->getClass($namespace);

        if ($class && $this->hasConstructor($fileSegments)) {
            [$params, $stmts] = $this->getParamsAndStmts($fileSegments);
            $class->stmts[] = new ClassMethod(
                '__construct',
                [
                    'flags' => Class_::MODIFIER_PUBLIC,
                    'params' => $params,
                    'stmts' => $stmts,
                ]
            );
        }

        return $statements;
    }

    protected function getParamsAndStmts(array $fileSegments): array
    {
        $params = [];
        $stmts = [];

        /** @var FileSegments $fileSegment */
        foreach ($fileSegments as $fileSegment) {
            $constructor = $fileSegment->getConstructor();
            if (!$constructor) {
                continue;
            }
            /** @var Param $param */
            foreach ($constructor->params as $param) {
                if (isset($params[$param->var->name])) {
                    continue;
                }
                $params[$param->var->name] = $param;
            }
            $stmts = [...$stmts, ...$constructor->stmts];
        }

        return [$params, $stmts];
    }

    protected function hasConstructor(array $fileSegments): bool
    {
        $result = false;

        /** @var FileSegments $fileSegment */
        foreach ($fileSegments as $fileSegment) {
            if ($fileSegment->getConstructor()) {
                $result = true;
                break;
            }
        }

        return $result;
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