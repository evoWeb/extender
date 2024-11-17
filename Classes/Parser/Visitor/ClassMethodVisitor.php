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

namespace Evoweb\Extender\Parser\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;

class ClassMethodVisitor extends AbstractVisitor
{
    /**
     * @var string[]
     */
    protected array $disallowedMethodNames = [
        '__construct',
        'initializeObject',
    ];

    /**
     * @return int|Node|Node[]|null
     */
    public function enterNode(Node $node): int|null|Node|array
    {
        if (
            $node instanceof ClassMethod
            && !in_array((string)$node->name, $this->disallowedMethodNames)
        ) {
            $this->fileSegment->addMethod($node);
        }
        return $node;
    }
}