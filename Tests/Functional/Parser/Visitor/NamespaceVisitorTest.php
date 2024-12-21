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
use Evoweb\Extender\Parser\Visitor\NamespaceVisitor;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\Test;

class NamespaceVisitorTest extends AbstractTestBase
{
    #[Test]
    public function enterNode(): void
    {
        $fileSegments = new FileSegments();
        $visitor = new NamespaceVisitor($fileSegments);

        $namespaceName = new Name('TestVendor\TestNamespace');
        $namespace = new Namespace_($namespaceName);

        $class = new Class_('TestClass');

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse([$namespace, $class]);

        $this->assertEquals($namespaceName, $fileSegments->getNamespace());
    }

    #[Test]
    public function parsedCode(): void
    {
        $code = <<<'CODE'
<?php

namespace TestVendor\TestNamespace;

class TestClass
{
}
CODE;
        $parser = (new ParserFactory())->createForVersion(PhpVersion::fromComponents(8, 2));
        $ast = $parser->parse($code);

        $fileSegments = new FileSegments();
        $visitor = new NamespaceVisitor($fileSegments);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        $this->assertEquals('TestVendor\TestNamespace', $fileSegments->getNamespace()->name);
    }
}
