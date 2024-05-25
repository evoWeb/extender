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
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;

class UseGenerator implements GeneratorInterface
{
    public function generate(array $statements, array $fileSegments): array
    {
        $namespace = $this->getNamespace($statements);

        $uses = $this->getUniqueUses($fileSegments);
        foreach ($uses as $use) {
            $namespace->stmts[] = new Use_([$use]);
        }

        return $statements;
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

    protected function getUniqueUses(array $fileSegments): array
    {
        $uses = [];
        /** @var FileSegments $fileSegment */
        foreach ($fileSegments as $fileSegment) {
            foreach ($fileSegment->getUses() as $use) {
                $name = $use->name . $use->getAlias();
                if (isset($uses[$name])) {
                    continue;
                }
                $uses[$name] = $use;
            }
        }
        return array_values($uses);
    }
}
