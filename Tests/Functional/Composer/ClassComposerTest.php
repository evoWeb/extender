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

namespace Evoweb\Extender\Tests\Functional\Composer;

use Evoweb\Extender\Composer\ClassComposer;
use Evoweb\Extender\Composer\Generator\NamespaceGenerator;
use Evoweb\Extender\Parser\FileSegments;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PHPUnit\Framework\Attributes\Test;
use PhpParser\Node\PropertyItem;
use PhpParser\Node\Stmt\Property;

class ClassComposerTest extends AbstractTestBase
{
    #[Test]
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
        $fileSegments->setNamespace(new Node\Name('Fixture\BaseExtension\Domain\Model'));
        $fileSegments->addUseUse(new Stmt\UseUse(new Node\Name('Evoweb\Domain\Model\Test')));
        $fileSegments->setClass(new Stmt\Class_('ComposeMergedFileCode'));
        $fileSegments->addTrait(new Stmt\TraitUse([new Node\Name('Evoweb\TestTrait')]));
        // @phpstan-ignore argument.type
        $fileSegments->addProperty(new Property(2, [new PropertyItem('testProperty')]));
        $fileSegments->setConstructor(new Stmt\ClassMethod('__construct'));
        $fileSegments->addMethod(new Stmt\ClassMethod('getTestProperty'));

        $subject = new ClassComposer();

        $expected = $this->getExpected(__CLASS__ . '-' . __FUNCTION__, $basePath);

        $actual = json_encode($subject->composeMergedFileCode([$fileSegments]));

        self::assertEquals($expected, $actual);
    }

    #[Test]
    public function addFileStatement(): void
    {
        $subject = new class () extends ClassComposer {
            /**
             * @param Node[] $statements
             * @param FileSegments[] $fileSegments
             * @return Node[]
             */
            public function addFileStatement(
                array $statements,
                array $fileSegments,
                string $generatorClassName
            ): array {
                return parent::addFileStatement($statements, $fileSegments, $generatorClassName);
            }
        };

        $namespaceName = new Node\Name('Fixture\BaseExtension\Domain\Model');

        $fileSegments = new FileSegments();
        $fileSegments->setBaseClass(true);
        $fileSegments->setNamespace($namespaceName);

        $expected = [new Node\Stmt\Namespace_($namespaceName)];

        $actual = $subject->addFileStatement([], [$fileSegments], NamespaceGenerator::class);

        self::assertEquals($expected, $actual);
    }
}
