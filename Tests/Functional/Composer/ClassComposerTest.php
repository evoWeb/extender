<?php

namespace Evoweb\Extender\Tests\Functional\Composer;

use Evoweb\Extender\Composer\ClassComposer;
use Evoweb\Extender\Composer\Generator\NamespaceGenerator;
use Evoweb\Extender\Parser\FileSegments;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class ClassComposerTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function composeMergedFileCode(): void
    {
        $basePath = 'base.php';

        $code = file_get_contents(realpath(
            __DIR__ . '/../../Fixtures/Extensions/base_extension/Classes/Domain/Model/ComposeMergedFileCode.php'
        ));

        $fileSegments = new FileSegments();
        $fileSegments->setFilePath($basePath);
        $fileSegments->setBaseClass(true);
        $fileSegments->setCode($code);
        $fileSegments->setNamespace(new Node\Name('Evoweb\TestNamespace'));
        $fileSegments->addUseUse(new Stmt\UseUse(new Node\Name('Evoweb\Domain\Model\Test')));
        $fileSegments->setClass(new Stmt\Class_('ComposeMergedFileCode'));
        $fileSegments->addTrait(new Stmt\TraitUse([new Node\Name('Evoweb\TestTrait')]));
        $fileSegments->addProperty(new Stmt\Property(2, [new Stmt\PropertyProperty('testProperty')]));
        $fileSegments->setConstructor(new Stmt\ClassMethod('__construct'));
        $fileSegments->addMethod(new Stmt\ClassMethod('getTestProperty'));

        $subject = new ClassComposer();

        $expected = $this->getExpected(__CLASS__ . '-' . __FUNCTION__, $basePath);

        $actual = json_encode($subject->composeMergedFileCode([$fileSegments]));

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function addFileStatement(): void
    {
        $subject = new class () extends ClassComposer {
            public function addFileStatement(
                array $statements,
                array $fileSegments,
                string $generatorClassName
            ): array {
                return parent::addFileStatement($statements, $fileSegments, $generatorClassName);
            }
        };

        $namespaceName = new Node\Name('Evoweb\TestNamespace');

        $fileSegments = new FileSegments();
        $fileSegments->setBaseClass(true);
        $fileSegments->setNamespace($namespaceName);

        $expected = [new Node\Stmt\Namespace_($namespaceName)];

        $actual = $subject->addFileStatement([], [$fileSegments], NamespaceGenerator::class);

        self::assertEquals($expected, $actual);
    }
}
