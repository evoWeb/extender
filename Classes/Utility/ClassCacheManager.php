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

namespace Evoweb\Extender\Utility;

use Composer\Autoload\ClassLoader;
use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Exception\FileNotFoundException;
use TYPO3\CMS\Core\Cache\Exception\InvalidDataException;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassCacheManager
{
    protected PhpFrontend $classCache;

    protected ClassLoader $composerClassLoader;

    protected Register $register;

    protected array $constructorLines = [];

    public function __construct(
        PhpFrontend $classCache,
        ClassLoader $composerClassLoader,
        Register $register
    ) {
        $this->classCache = $classCache;
        $this->composerClassLoader = $composerClassLoader;
        $this->register = $register;
    }

    public function build(string $cacheEntryIdentifier, string $className): void
    {
        // Get the file to extend, this needs to be loaded as first
        $path = $this->composerClassLoader->findFile($className);
        if (!is_file($path)) {
            throw new FileNotFoundException('Base file "' . $path . '" does not exist');
        }
        $code = $this->parseSingleFile($path, false);

        // Get the files from all other extensions that are extending this domain model
        foreach ($this->register->getExtendingClasses($className) as $extendingEntity) {
            $path = $this->composerClassLoader->findFile($extendingEntity);
            if (!is_file($path)) {
                throw new FileNotFoundException('Extending file "' . $path . '" does not exist');
            }
            $code .= $this->parseSingleFile($path);
        }

        if (count($this->constructorLines)) {
            $code .= implode(LF, $this->constructorLines) . LF . '    }' . LF;
            // reset constructor lines
            $this->constructorLines = [];
        }

        // Close the class definition
        $code = $this->closeClassDefinition($code);

        // Add the new file to the class cache
        $this->classCache->set($cacheEntryIdentifier, $code);
    }

    /**
     * Parse a single file and does some magic
     * - Remove the php tags
     * - Remove the class definition (if set)
     *
     * @param string $filePath path of the file
     * @param bool $removeClassDefinition If class definition should be removed
     *
     * @return string path of the saved file
     * @throws \InvalidArgumentException
     */
    protected function parseSingleFile(string $filePath, bool $removeClassDefinition = true): string
    {
        $code = GeneralUtility::getUrl($filePath);
        return $this->changeCode($code, $filePath, $removeClassDefinition);
    }

    /**
     * Strip php file parts that should not be used in the concatenation
     *
     * @param string $code
     * @param string $filePath
     * @param bool $removeClassDefinition
     * @param bool $renderPartialInfo
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function changeCode(
        string $code,
        string $filePath,
        bool $removeClassDefinition = true,
        bool $renderPartialInfo = true
    ): string {
        if (empty($code)) {
            throw new \InvalidArgumentException(sprintf('File "%s" could not be fetched or is empty', $filePath));
        }

        /** @var ClassParser $classParser */
        $classParser = GeneralUtility::makeInstance(ClassParser::class);
        $classParser->parse($code);
        $classParserInformation = $classParser->getFirstClass();

        $code = str_replace('<?php', '', $code);
        $codeInLines = explode(LF, str_replace(CR, '', $code));
        $offsetForInnerPart = 0;

        if ($removeClassDefinition) {
            $offsetForInnerPart = $classParserInformation['start'];
            if (isset($classParserInformation['eol'])) {
                $innerPart = array_slice(
                    $codeInLines,
                    $classParserInformation['start'],
                    ($classParserInformation['eol'] - $classParserInformation['start'] - 1)
                );
            } else {
                $innerPart = array_slice($codeInLines, $classParserInformation['start']);
            }
        } else {
            $innerPart = $codeInLines;
        }

        if (trim($innerPart[0]) === '{') {
            unset($innerPart[0]);
        }

        // unset the constructor and save its lines
        if (isset($classParserInformation['functions']['__construct'])) {
            $constructorInfo = $classParserInformation['functions']['__construct'];
            if (count($this->constructorLines) > 0) {
                $start = $constructorInfo['start'] - $offsetForInnerPart;
                unset($innerPart[$start - 1]);
            } else {
                $start = $constructorInfo['start'] - $offsetForInnerPart - 1;
            }
            for ($i = $start; $i < $constructorInfo['end'] - $offsetForInnerPart; $i++) {
                if (trim($innerPart[$i]) === '{' && count($this->constructorLines) > 1) {
                    unset($innerPart[$i]);
                    continue;
                }
                $this->constructorLines[] = $innerPart[$i];
                unset($innerPart[$i]);
            }
            unset($innerPart[$constructorInfo['end'] - $offsetForInnerPart]);
        }

        $content = implode(LF, $innerPart);
        $closingBracket = strrpos($content, '}');
        $content = substr($content, 0, $closingBracket);

        // Add some information for each partial
        if ($renderPartialInfo) {
            $content = $this->getPartialInfo($filePath) . $content;
        }

        return $content . LF;
    }

    /**
     * Add partial information about file from which code gets added
     *
     * @param string $filePath
     *
     * @return string
     */
    protected function getPartialInfo(string $filePath): string
    {
        $comment = [];
        $comment[] = '/' . str_repeat('*', 71);
        $comment[] = ' * this is partial from:';
        $comment[] = ' *  ' . str_replace(Environment::getPublicPath() . '/', '', $filePath);
        $comment[] = ' ' . str_repeat('*', 70) . '/' . LF;

        return implode(LF, $comment);
    }

    /**
     * Add curly brace at the end of the class
     *
     * @param string $code
     *
     * @return string
     */
    protected function closeClassDefinition(string $code): string
    {
        return $code . LF . '}';
    }
}
