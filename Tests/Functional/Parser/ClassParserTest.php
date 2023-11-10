<?php

namespace Evoweb\Extender\Tests\Functional\Parser;

use Evoweb\Extender\Parser\ClassParser;
use Evoweb\Extender\Parser\FileSegments;
use Evoweb\Extender\Parser\Visitor\NamespaceVisitor;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Parser\Php7;
use PhpParser\ParserFactory;
use PHPUnit\Framework\MockObject\MockObject;

class ClassParserTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function getFileSegments(): void
    {
        /** @var Php7|MockObject $parser */
        $parser = $this->getMockBuilder(Php7::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser->expects($this->once())->method('parse')->willReturn([
            new Stmt\Namespace_(new Node\Name('Evoweb\TestNamespace')),
            new Stmt\UseUse(new Node\Name('Evoweb\Domain\Model\Test')),
            new Stmt\Class_('GetFileSegments'),
            new Stmt\TraitUse([new Node\Name('Evoweb\TestTrait')]),
            new Stmt\Property(2, [new Stmt\PropertyProperty('testProperty')]),
            new Stmt\ClassMethod('__construct'),
            new Stmt\ClassMethod('getTestProperty'),
        ]);

        /** @var ParserFactory|MockObject $parserFactory */
        $parserFactory = $this->createMock(ParserFactory::class);
        $parserFactory->expects($this->once())->method('create')->willReturn($parser);

        $subject = new ClassParser($parserFactory);

        $basePath = realpath(
            __DIR__ . '/../../Fixtures/Extensions/base_extension/Classes/Domain/Model/GetFileSegments.php'
        );
        $pathSegment = realpath(__DIR__ . '/../../Fixtures/Extensions/');

        $expected = $this->getExpected(__CLASS__ . '-' . __FUNCTION__ . $this->getPhpVersion());

        $fileSegments = $subject->getFileSegments($basePath);
        $fileSegments->setFilePath(str_replace($pathSegment, '', $fileSegments->getFilePath()));
        $actual = json_encode($fileSegments);

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getFileSegment(): void
    {
        /** @var ParserFactory|MockObject $parser */
        $parserFactory = $this->createMock(ParserFactory::class);

        $subject = new class($parserFactory) extends ClassParser {
            public function traverseStatements(FileSegments $fileSegment, string $visitorClassName): void
            {
                parent::traverseStatements($fileSegment, $visitorClassName);
            }
        };

        $expected = new Node\Name('Evoweb\TestNamespace');

        $fileSegment = new FileSegments();
        $fileSegment->setStatements([new Stmt\Namespace_($expected)]);

        $subject->traverseStatements($fileSegment, NamespaceVisitor::class);

        $actual = $fileSegment->getNamespace();

        self::assertEquals($expected, $actual);
    }
}
