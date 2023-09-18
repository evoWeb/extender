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

namespace Evoweb\Extender\Parser\Visitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

class NamespaceVisitor extends NodeVisitorAbstract implements VisitorInterface
{
    protected ?Name $namespace = null;

    public function enterNode(Node $node): void
    {
        if ($node instanceof Namespace_) {
            $this->namespace = $node->name;
        }
    }

    public function getResult(): ?Name
    {
        return $this->namespace;
    }
}
