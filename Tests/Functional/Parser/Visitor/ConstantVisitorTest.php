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
use Evoweb\Extender\Parser\Visitor\ConstantVisitor;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\Test;

class ConstantVisitorTest extends AbstractTestBase
{
    #[Test]
    public function enterNode(): void
    {
        $fileSegments = new FileSegments();
        $visitor = new ConstantVisitor($fileSegments);

        $constant1 = new ClassConst([new Const_('Constant1', new Int_(1))]);
        $constant2 = new ClassConst([new Const_('Constant2', new Int_(2))]);

        $class = new Class_('TestClass');
        $class->stmts[] = $constant1;
        $class->stmts[] = $constant2;

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse([$class]);

        $this->assertEquals($constant2, $fileSegments->getClassConsts()[1]);
    }

    #[Test]
    public function parsedCode(): void
    {
        $code = <<<'CODE'
<?php

class TestClass
{
    protected const constant1 = '';
    protected const constant2 = '';
}
CODE;
        $parser = (new ParserFactory())->createForVersion(PhpVersion::fromComponents(8, 2));
        $ast = $parser->parse($code);

        $fileSegments = new FileSegments();
        $visitor = new ConstantVisitor($fileSegments);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        $this->assertEquals('constant2', $fileSegments->getClassConsts()[1]->consts[0]->name);
    }
}
