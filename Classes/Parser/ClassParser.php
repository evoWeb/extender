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

namespace Evoweb\Extender\Parser;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class ClassParser
{
    protected ParserFactory $parserFactory;

    protected array $visitors = [
        Visitor\NamespaceVisitor::class,
        Visitor\UseVisitor::class,
        Visitor\ClassVisitor::class,
        Visitor\TraitVisitor::class,
        Visitor\PropertyVisitor::class,
        Visitor\ConstructorVisitor::class,
        Visitor\ClassMethodVisitor::class,
    ];

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    public function getFileSegments(string $filePath): FileSegments
    {
        $fileSegments = new FileSegments();
        $fileSegments->setFilePath($filePath);
        $fileSegments->setCode(file_get_contents($filePath));

        try {
            $parser = $this->parserFactory->create(ParserFactory::ONLY_PHP7);
            $fileSegments->setStatements($parser->parse($fileSegments->getCode()));

            foreach ($this->visitors as $visitor) {
                $this->traverseStatements($fileSegments, $visitor);
            }
        } catch (\Exception $e) {}

        return $fileSegments;
    }

    protected function traverseStatements(FileSegments $fileSegment, string $visitorClassName): void
    {
        $visitor = new $visitorClassName($fileSegment);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($fileSegment->getStatements());
    }
}
