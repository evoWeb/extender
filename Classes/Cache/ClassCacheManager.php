<?php

declare(strict_types=1);

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

namespace Evoweb\Extender\Cache;

use Composer\Autoload\ClassLoader;
use Evoweb\Extender\Composer\ClassComposer;
use Evoweb\Extender\Configuration\ClassRegister;
use Evoweb\Extender\Exception\BaseFileNotFoundException;
use Evoweb\Extender\Exception\ExtendingFileNotFoundException;
use Evoweb\Extender\Parser\ClassParser;
use Evoweb\Extender\Parser\FileSegments;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class ClassCacheManager
{
    public function __construct(
        protected FrontendInterface $classCache,
        protected ClassLoader $classLoader,
        protected ClassParser $classParser,
        protected ClassComposer $classComposer,
        protected ClassRegister $classRegister
    ) {}

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
            ...$this->getExtendingClassesFileSegments($className),
        ];

        $code = $this->getMergedFileCode($fileSegments);
        $this->addFileToCache($cacheEntryIdentifier, $code);
    }

    protected function getBaseClassFileSegments(string $className): FileSegments
    {
        return $this->getFileSegments($className, true, BaseFileNotFoundException::class);
    }

    /**
     * @return FileSegments[]
     */
    protected function getExtendingClassesFileSegments(string $baseClassName): array
    {
        $filesSegments = [];

        foreach ($this->classRegister->getExtendingClasses($baseClassName) as $className) {
            $filesSegments[] = $this->getFileSegments($className, false, ExtendingFileNotFoundException::class);
        }

        return $filesSegments;
    }

    protected function getFileSegments(string $className, bool $baseClass, string $exceptionClass): FileSegments
    {
        $type = $baseClass ? 'base' : 'extend';
        $filePath = $this->classLoader->findFile($className);
        $filePath = realpath($filePath);

        if ($filePath === false) {
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

    /**
     * @param FileSegments[] $fileSegments
     */
    protected function getMergedFileCode(array $fileSegments): string
    {
        return $this->classComposer->composeMergedFileCode($fileSegments);
    }

    protected function addFileToCache(string $cacheEntryIdentifier, string $code): void
    {
        try {
            $this->classCache->set($cacheEntryIdentifier, $code);
        } catch (\Exception) {
        }
    }
}
