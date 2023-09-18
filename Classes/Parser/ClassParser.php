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

use Evoweb\Extender\Parser\Visitor\VisitorInterface;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class ClassParser
{
    protected ParserFactory $parserFactory;

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    public function getFileSegments(string $code): array
    {
        try {
            $parser = $this->parserFactory->create(ParserFactory::ONLY_PHP7);
            $statements = $parser->parse($code);

            $fileSegments = [
                'code' => $code,
                'base' => $statements,
                'namespace' => $this->getFileSegment($statements, new Visitor\NamespaceVisitor()),
                'uses' => $this->getFileSegment($statements, new Visitor\UseVisitor()),
                'class' => $this->getFileSegment($statements, new Visitor\ClassVisitor()),
                'traits' => $this->getFileSegment($statements, new Visitor\TraitVisitor()),
                'properties' => $this->getFileSegment($statements, new Visitor\PropertyVisitor()),
                'constructor' => $this->getFileSegment($statements, new Visitor\ConstructorVisitor()),
                'functions' => $this->getFileSegment($statements, new Visitor\ClassMethodVisitor()),
            ];
        } catch (Error $e) {
            $fileSegments = [];
        }

        return $fileSegments;
    }

    protected function getFileSegment(array $statements, VisitorInterface $visitor): mixed
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($statements);

        return $visitor->getResult();
    }
}
