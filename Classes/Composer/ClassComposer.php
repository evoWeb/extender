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

namespace Evoweb\Extender\Composer;

use Evoweb\Extender\Composer\Generator\GeneratorInterface;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

class ClassComposer
{
    public function mergeFileSegments(array $fileSegments): string
    {
        $statements = [];
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\NamespaceGenerator());
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\FileCommentGenerator());
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\UseGenerator());
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\ClassGenerator());
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\TraitGenerator());
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\PropertyGenerator());
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\ConstructorGenerator());
        $statements = $this->addFileStatement($statements, $fileSegments, new Generator\ClassMethodGenerator());

        $prettyPrinter = new PrettyPrinter();
        $fileCode = $prettyPrinter->prettyPrintFile($statements);

        return str_replace('<?php' . chr(10), '', $fileCode);
    }

    protected function addFileStatement(array $statements, array $fileSegments, GeneratorInterface $generator): array
    {
        return $generator->generate($statements, $fileSegments);
    }
}
