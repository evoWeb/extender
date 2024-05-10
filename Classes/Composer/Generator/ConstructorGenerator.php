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
use PhpParser\Modifiers;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
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
                    'flags' => Modifiers::PUBLIC,
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

            $params = $this->getConstructorParameter($params, $constructor->params);
            $stmts = $this->getConstructorStatements($stmts, $constructor->stmts, $fileSegment->isBaseClass());
        }

        return [$params, $stmts];
    }

    protected function getConstructorParameter(array $result, array $params): array
    {
        /** @var Param $param */
        foreach ($params as $param) {
            if (isset($result[$param->var->name])) {
                continue;
            }
            $result[$param->var->name] = $param;
        }

        return $result;
    }

    protected function getConstructorStatements(array $result, array $stmts, bool $isBaseClass): array
    {
        if ($isBaseClass) {
            $result = [...$result, ...$stmts];
        } else {
            /** @var Expression $stmt */
            foreach ($stmts as $stmt) {
                $expr = $stmt->expr;
                if (
                    !(
                        $expr instanceof StaticCall
                        && (string)$expr->class === 'parent'
                        && (string)$expr->name === '__construct'
                    )
                ) {
                    $result[] = $stmt;
                }
            }
        }

        return $result;
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
