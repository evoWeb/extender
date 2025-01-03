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

namespace Evoweb\Extender\Composer;

use Evoweb\Extender\Composer\Generator\GeneratorInterface;
use Evoweb\Extender\Parser\FileSegments;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

class ClassComposer
{
    /**
     * @var string[]
     */
    protected array $generators = [
        Generator\FileCommentGenerator::class,
        Generator\NamespaceGenerator::class,
        Generator\UseGenerator::class,
        Generator\ClassGenerator::class,
        Generator\TraitGenerator::class,
        Generator\ConstantGenerator::class,
        Generator\PropertyGenerator::class,
        Generator\ConstructorGenerator::class,
        Generator\InitializeObjectGenerator::class,
        Generator\ClassMethodGenerator::class,
    ];

    /**
     * @param FileSegments[] $fileSegments
     */
    public function composeMergedFileCode(array $fileSegments): string
    {
        $statements = [];

        foreach ($this->generators as $generator) {
            $statements = $this->addFileStatement($statements, $fileSegments, $generator);
        }

        $prettyPrinter = new PrettyPrinter();
        $fileCode = $prettyPrinter->prettyPrintFile($statements);

        return str_replace('<?php' . chr(10), '', $fileCode);
    }

    /**
     * @param Node[] $statements
     * @param FileSegments[] $fileSegments
     * @return Node[]
     */
    protected function addFileStatement(
        array $statements,
        array $fileSegments,
        string $generatorClassName
    ): array {
        /** @var GeneratorInterface $generator */
        $generator = new $generatorClassName();
        return $generator->generate($statements, $fileSegments);
    }
}
