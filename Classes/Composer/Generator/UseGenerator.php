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

use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

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

    protected function getNamespace(array $statements): Namespace_
    {
        return $statements[0];
    }

    protected function getUniqueUses(array $fileSegments): array
    {
        $uses = [];
        foreach ($fileSegments as $fileSegment) {
            /** @var UseUse $use */
            foreach ($fileSegment['uses'] as $use) {
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
