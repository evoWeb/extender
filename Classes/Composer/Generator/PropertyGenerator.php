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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;

class PropertyGenerator implements GeneratorInterface
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
            $class->stmts = [...$class->stmts, ...$this->getUniqueProperties($fileSegments)];
        }

        return $statements;
    }

    /**
     * @param FileSegments[] $fileSegments
     * @return Property[]
     */
    protected function getUniqueProperties(array $fileSegments): array
    {
        $properties = [];
        foreach ($fileSegments as $fileSegment) {
            foreach ($fileSegment->getProperties() as $property) {
                if (count($property->props) == 1) {
                    $propertyProperty = $property->props[0];
                    $properties[(string)$propertyProperty->name] = $property;
                } else {
                    foreach ($property->props as $propertyProperty) {
                        $properties[(string)$propertyProperty->name] = new Property(
                            Modifiers::PROTECTED,
                            [$propertyProperty]
                        );
                    }
                }
            }
        }
        return array_values($properties);
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
