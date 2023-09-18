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

namespace Evoweb\Extender\Composer\Generator;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Namespace_;

class FileCommentGenerator implements GeneratorInterface
{
    public function generate(array $statements, array $fileSegments): array
    {
        $commentText = $this->createCommentText($fileSegments);

        $namespace = $this->getNamespace($statements);
        $namespace->setDocComment(new Doc($commentText));

        return $statements;
    }

    protected function getNamespace(array $statements): Namespace_
    {
        return $statements[0];
    }

    protected function createCommentText(array $fileSegments): string
    {
        $fileComment = [
            '/*',
            ' * This file is composed by "extender"',
            ' * Merged class with class parts of:',
        ];

        foreach ($fileSegments as $fileSegment) {
            $fileComment[] = ' *  - ' . $fileSegment['filePath'];
        }

        $fileComment[] = ' */';

        return implode(chr(10), $fileComment) . chr(10);
    }
}
