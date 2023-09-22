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

use Evoweb\Extender\Parser\FileSegments;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Nop;

class FileCommentGenerator implements GeneratorInterface
{
    public function generate(array $statements, array $fileSegments): array
    {
        $commentText = $this->createCommentText($fileSegments);

        $nop = new Nop();
        $nop->setDocComment(new Doc($commentText));

        $statements[] = $nop;

        return $statements;
    }

    protected function createCommentText(array $fileSegments): string
    {
        $fileComment = [
            '/*',
            ' * This file is composed by "extender"',
            ' * Merged class with parts of files:',
        ];

        /** @var FileSegments $fileSegment */
        foreach ($fileSegments as $fileSegment) {
            $fileComment[] = ' *  - ' . $fileSegment->getFilePath();
        }

        $fileComment[] = ' */';

        return implode(chr(10), $fileComment) . chr(10);
    }
}
