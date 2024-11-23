<?php

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

namespace Evoweb\Extender\Tests\Functional\Parser\Visitor;

use Evoweb\Extender\Parser\FileSegments;
use Evoweb\Extender\Parser\Visitor\InitializeObjectVisitor;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\Test;

class InitializeObjectVisitorTest extends AbstractTestBase
{
    #[Test]
    public function enterNode(): void
    {
        $fileSegments = new FileSegments();
        $visitor = new InitializeObjectVisitor($fileSegments);

        $constructMethode = new ClassMethod('__construct');
        $initializeObjectMethode = new ClassMethod('initializeObject');

        $class = new Class_('TestClass');
        $class->stmts[] = $constructMethode;
        $class->stmts[] = $initializeObjectMethode;

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse([$class]);

        $this->assertEquals($initializeObjectMethode, $fileSegments->getInitializeObject());
    }

    #[Test]
    public function parsedCode(): void
    {
        $code = <<<'CODE'
<?php

class TestClass
{
    public function __construct() {}
    public function initializeObject() {}
}
CODE;
        $parser = (new ParserFactory())->createForVersion(PhpVersion::fromComponents(8, 2));
        $ast = $parser->parse($code);

        $fileSegments = new FileSegments();
        $visitor = new InitializeObjectVisitor($fileSegments);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        $this->assertEquals('initializeObject', $fileSegments->getInitializeObject()->name);
    }
}
