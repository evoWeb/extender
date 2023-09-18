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
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class ClassCacheManager
{
    protected FrontendInterface $classCache;

    protected ClassLoader $composerClassLoader;

    protected ClassParser $classParser;

    protected ClassComposer $classComposer;

    protected Register $register;

    protected array $constructorLines = [];

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
     * Build cache for base and extending files
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

        $code = $this->mergeFileSegments($fileSegments);
        $this->addFileToCache($cacheEntryIdentifier, $code);
    }

    protected function getBaseClassFileSegments(string $baseClassName): array
    {
        $filePath = $this->composerClassLoader->findFile($baseClassName);
        if ($filePath === false) {
            throw new BaseFileNotFoundException(
                'Composer did not find the file path for base class "' . $baseClassName
            );
        }
        if (!is_file($filePath)) {
            throw new BaseFileNotFoundException(
                'File "' . $filePath . '" for base class "' . $baseClassName . '" does not exist'
            );
        }
        return $this->getFileSegments(realpath($filePath), true);
    }

    protected function getExtendingClassesFileSegments(string $baseClassName): array
    {
        $filesSegments = [];

        foreach ($this->register->getExtendingClasses($baseClassName) as $extendingClassName) {
            $filePath = $this->composerClassLoader->findFile($extendingClassName);
            if ($filePath === false) {
                throw new ExtendingFileNotFoundException(
                    'Composer did not find the file path for extending class "' . $extendingClassName
                );
            }
            if (!is_file($filePath)) {
                throw new ExtendingFileNotFoundException(
                    'File "' . $filePath . '" for extending class "' . $extendingClassName . '" does not exist'
                );
            }
            $filesSegments[] = $this->getFileSegments(realpath($filePath), false);
        }

        return $filesSegments;
    }

    protected function getFileSegments(string $filePath, bool $baseClass): array
    {
        $code = file_get_contents($filePath);

        $fileSegments = $this->classParser->getFileSegments($code);
        $fileSegments['filePath'] = $filePath;
        $fileSegments['baseClass'] = $baseClass;

        return $fileSegments;
    }

    protected function mergeFileSegments(array $fileSegments): string
    {
        return $this->classComposer->mergeFileSegments($fileSegments);
    }

    protected function addFileToCache(string $cacheEntryIdentifier, string $code): void
    {
        try {
            $this->classCache->set($cacheEntryIdentifier, $code);
        } catch (\Exception $e) {}
    }
}
