<?php

namespace Evoweb\Extender\Tests\Functional\Composer\Generator;

use Evoweb\Extender\Composer\Generator\FileCommentGenerator;
use Evoweb\Extender\Parser\FileSegments;
use Evoweb\Extender\Tests\Functional\AbstractTestBase;

class FileCommentGeneratorTest extends AbstractTestBase
{
    /**
     * @test
     */
    public function generate(): void
    {
        $baseFileSegment = new FileSegments();
        $baseFileSegment->setFilePath('base.php');
        $extendFileSegment = new FileSegments();
        $extendFileSegment->setFilePath('extend.php');
        $statements = [
            $baseFileSegment,
            $extendFileSegment,
        ];

        $subject = new FileCommentGenerator();
        $statements = $subject->generate([], $statements);

        $expected = $this->getExpected(
            __CLASS__ . '-' . __FUNCTION__,
            $baseFileSegment->getFilePath(),
            $extendFileSegment->getFilePath()
        );

        $actual = $this->convertStatementsIntoCode($statements);

        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function createCommentText(): void
    {
        $baseFileSegment = new FileSegments();
        $baseFileSegment->setFilePath('base.php');
        $extendFileSegment = new FileSegments();
        $extendFileSegment->setFilePath('extend.php');
        $statements = [
            $baseFileSegment,
            $extendFileSegment,
        ];

        $subject = new class() extends FileCommentGenerator {
            public function createCommentText(array $fileSegments): string
            {
                return parent::createCommentText($fileSegments);
            }
        };

        $expected = $this->getExpected(
            __CLASS__ . '-' . __FUNCTION__,
            $baseFileSegment->getFilePath(),
            $extendFileSegment->getFilePath()
        );

        $actual = trim($subject->createCommentText($statements));

        self::assertEquals($expected, $actual);
    }
}
