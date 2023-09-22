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

namespace Evoweb\Extender\Cache;

use Composer\Autoload\ClassLoader;
use Evoweb\Extender\Composer\ClassComposer;
use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Exception\BaseFileNotFoundException;
use Evoweb\Extender\Exception\ExtendingFileNotFoundException;
use Evoweb\Extender\Parser\ClassParser;
use Evoweb\Extender\Parser\FileSegments;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class ClassCacheManager
{
    protected FrontendInterface $classCache;

    protected ClassLoader $composerClassLoader;

    protected ClassParser $classParser;

    protected ClassComposer $classComposer;

    protected Register $register;

    public function __construct(
        FrontendInterface $classCache,
        ClassLoader $composerClassLoader,
        ClassParser $classParser,
        ClassComposer $classComposer,
        Register $register
    ) {
        $this->classCache = $classCache;
        $this->composerClassLoader = $composerClassLoader;
        $this->classParser = $classParser;
        $this->classComposer = $classComposer;
        $this->register = $register;
    }

    /**
     * Build merged file and cache for base and extending files
     *
     * @throws BaseFileNotFoundException
     * @throws ExtendingFileNotFoundException
     */
    public function build(string $cacheEntryIdentifier, string $className): void
    {
        $fileSegments = [
            $this->getBaseClassFileSegments($className),
            ...$this->getExtendingClassesFileSegments($className)
        ];

        $code = $this->getMergedFileCode($fileSegments);
        $this->addFileToCache($cacheEntryIdentifier, $code);
    }

    protected function getBaseClassFileSegments(string $className): FileSegments
    {
        return $this->getFileSegments($className, true, BaseFileNotFoundException::class);
    }

    protected function getExtendingClassesFileSegments(string $baseClassName): array
    {
        $filesSegments = [];

        foreach ($this->register->getExtendingClasses($baseClassName) as $className) {
            $filesSegments[] = $this->getFileSegments($className, false, ExtendingFileNotFoundException::class);
        }

        return $filesSegments;
    }

    protected function getFileSegments(string $className, bool $baseClass, string $exceptionClass): FileSegments
    {
        $type = $baseClass ? 'base' : 'extend';
        $filePath = $this->composerClassLoader->findFile($className);

        if ($filePath === false || $filePath === '') {
            throw new $exceptionClass(
                'Composer did not find the file path for ' . $type . ' class "' . $className . '"'
            );
        }

        if (!is_file($filePath)) {
            throw new $exceptionClass(
                'File "' . $filePath . '" for ' . $type . ' class "' . $className . '" does not exist'
            );
        }

        $fileSegments = $this->classParser->getFileSegments($filePath);
        $fileSegments->setBaseClass($baseClass);

        return $fileSegments;
    }

    protected function getMergedFileCode(array $fileSegments): string
    {
        return $this->classComposer->composeMergedFileCode($fileSegments);
    }

    protected function addFileToCache(string $cacheEntryIdentifier, string $code): void
    {
        try {
            $this->classCache->set($cacheEntryIdentifier, $code);
        } catch (\Exception $e) {}
    }
}
