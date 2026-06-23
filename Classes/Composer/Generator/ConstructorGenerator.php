<?php

declare(strict_types=1);

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Composer\Generator;

use Evoweb\Extender\Parser\FileSegments;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;

class ConstructorGenerator implements GeneratorInterface
{
    /**
     * @param Node[] $statements
     * @param FileSegments[] $fileSegments
     * @return Node[]
     */
    public function generate(array $statements, array $fileSegments): array
    {
        $namespace = $this->getNamespace($statements);
        if ($namespace === null) {
            return $statements;
        }

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

    /**
     * @param FileSegments[] $fileSegments
     * @return array{0: Param[], 1: Stmt[]}
     */
    protected function getParamsAndStmts(array $fileSegments): array
    {
        $params = [];
        $stmts = [];
        foreach ($fileSegments as $fileSegment) {
            $constructor = $fileSegment->getConstructor();
            if (!$constructor) {
                continue;
            }

            $params = $this->getConstructorParameter($params, $constructor->params);
            if ($constructor->stmts !== null) {
                $stmts = $this->getConstructorStatements($stmts, $constructor->stmts, $fileSegment->isBaseClass());
            }
        }

        return [$params, $stmts];
    }

    /**
     * @param Param[] $result
     * @param Param[] $params
     * @return Param[]
     */
    protected function getConstructorParameter(array $result, array $params): array
    {
        foreach ($params as $param) {
            $name = $param->var instanceof Variable && is_string($param->var->name) ? $param->var->name : null;
            if ($name === null || isset($result[$name])) {
                continue;
            }
            $result[$name] = $param;
        }

        return $result;
    }

    /**
     * @param Stmt[] $result
     * @param Stmt[]|Expression[] $stmts
     * @param bool $isBaseClass
     * @return Stmt[]
     */
    protected function getConstructorStatements(array $result, array $stmts, bool $isBaseClass): array
    {
        if ($isBaseClass) {
            $result = [...$result, ...$stmts];
        } else {
            foreach ($stmts as $stmt) {
                /** @var Expression $stmt */
                $expr = $stmt->expr;
                if (
                    !(
                        $expr instanceof StaticCall
                        && $expr->class instanceof Name
                        && $expr->class->toString() === 'parent'
                        && $expr->name instanceof Identifier
                        && $expr->name->toString() === '__construct'
                    )
                ) {
                    $result[] = $stmt;
                }
            }
        }

        return $result;
    }

    /**
     * @param FileSegments[] $fileSegments
     */
    protected function hasConstructor(array $fileSegments): bool
    {
        $result = false;

        foreach ($fileSegments as $fileSegment) {
            if ($fileSegment->getConstructor()) {
                $result = true;
                break;
            }
        }

        return $result;
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
