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

namespace Evoweb\Extender\Parser;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;

class ClassParser
{
    /**
     * @var string[]
     */
    protected array $visitors = [
        Visitor\NamespaceVisitor::class,
        Visitor\UseVisitor::class,
        Visitor\ClassVisitor::class,
        Visitor\TraitVisitor::class,
        Visitor\ConstantVisitor::class,
        Visitor\PropertyVisitor::class,
        Visitor\ConstructorVisitor::class,
        Visitor\InitializeObjectVisitor::class,
        Visitor\ClassMethodVisitor::class,
    ];

    public function __construct(protected ParserFactory $parserFactory) {}

    public function getFileSegments(string $filePath): FileSegments
    {
        $fileSegments = new FileSegments();
        $fileSegments->setFilePath($filePath);
        $fileSegments->setCode(file_get_contents($filePath));

        try {
            // @extensionScannerIgnoreLine
            // @phpstan-ignore method.notFound
            $parser = $this->parserFactory->createForVersion(PhpVersion::fromComponents(8, 2));
            $fileSegments->setStatements($parser->parse($fileSegments->getCode()));

            foreach ($this->visitors as $visitor) {
                $this->traverseStatements($fileSegments, $visitor);
            }
        } catch (\Exception) {
        }

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
