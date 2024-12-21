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
use Evoweb\Extender\Parser\Visitor\PropertyVisitor;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\Test;

class PropertyVisitorTest extends AbstractTestBase
{
    #[Test]
    public function enterNode(): void
    {
        $fileSegments = new FileSegments();
        $visitor = new PropertyVisitor($fileSegments);

        $property1 = new Property(0, [], [], new Name('Property1'));
        $property2 = new Property(0, [], [], new Name('Property2'));

        $class = new Class_('TestClass');
        $class->stmts[] = $property1;
        $class->stmts[] = $property2;

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse([$class]);

        $this->assertEquals($property2, $fileSegments->getProperties()[1]);
    }

    #[Test]
    public function parsedCode(): void
    {
        $code = <<<'CODE'
<?php

namespace TestVendor\TestNamespace;

class TestClass
{
    protected int $property1 = 0;
    protected string $property2 = '';
}
CODE;
        $parser = (new ParserFactory())->createForVersion(PhpVersion::fromComponents(8, 2));
        $ast = $parser->parse($code);

        $fileSegments = new FileSegments();
        $visitor = new PropertyVisitor($fileSegments);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        $this->assertEquals('property2', $fileSegments->getProperties()[1]->props[0]->name);
    }
}
