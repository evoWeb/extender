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
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt;

class InitializeObjectGenerator implements GeneratorInterface
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

        if ($class && $this->hasInitializeObject($fileSegments)) {
            [$params, $stmts] = $this->getParamsAndStmts($fileSegments);
            $class->stmts[] = new ClassMethod(
                'initializeObject',
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
     * @return array<Param[]|Stmt[]>
     */
    protected function getParamsAndStmts(array $fileSegments): array
    {
        $params = [];
        $stmts = [];
        foreach ($fileSegments as $fileSegment) {
            $initializeObject = $fileSegment->getInitializeObject();
            if (!$initializeObject) {
                continue;
            }

            $params = $this->getInitializeObjectParameter($params, $initializeObject->getParams());
            $stmts = $this->getInitializeObjectStatements(
                $stmts,
                $initializeObject->getStmts(),
                $fileSegment->isBaseClass()
            );
        }

        return [$params, $stmts];
    }

    /**
     * @param Param[] $result
     * @param Param[] $params
     * @return Param[]
     */
    protected function getInitializeObjectParameter(array $result, array $params): array
    {
        foreach ($params as $param) {
            if (isset($result[$param->var->name])) {
                continue;
            }
            $result[$param->var->name] = $param;
        }

        return $result;
    }

    /**
     * @param Stmt[] $result
     * @param Stmt[]|Expression[] $stmts
     * @param bool $isBaseClass
     * @return Stmt[]
     */
    protected function getInitializeObjectStatements(array $result, array $stmts, bool $isBaseClass): array
    {
        if ($isBaseClass) {
            $result = [...$result, ...$stmts];
        } else {
            foreach ($stmts as $stmt) {
                /** @var Expression|StaticCall $stmt */
                $expr = $stmt->expr;
                if (
                    !(
                        $expr instanceof StaticCall
                        && (string)$expr->class === 'parent'
                        && (string)$expr->name === 'initializeObject'
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
    protected function hasInitializeObject(array $fileSegments): bool
    {
        $result = false;
        foreach ($fileSegments as $fileSegment) {
            if ($fileSegment->getInitializeObject()) {
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
