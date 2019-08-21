<?php
namespace Evoweb\Extender\Utility;

/**
 * This file is developed by evoweb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Composer\Autoload\ClassLoader;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class cache manager
 */
class ClassCacheManager
{
    /**
     * Cache instance
     *
     * @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend
     */
    protected $classCache;

    /**
     * @var ClassLoader
     */
    protected $composerClassLoader;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var array
     */
    protected $constructorLines = [];

    /**
     * Constructor
     *
     * @param ClassLoader $composerClassLoader
     * @param CacheManager $cacheManager
     */
    public function __construct(
        ClassLoader $composerClassLoader,
        CacheManager $cacheManager
    ) {
        $this->composerClassLoader = $composerClassLoader;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Rebuild the class cache
     *
     * @param array $parameters
     *
     * @throws \Evoweb\Extender\Exception\FileNotFoundException
     * @throws \TYPO3\CMS\Core\Cache\Exception\InvalidDataException
     */
    public function reBuild(array $parameters = array())
    {
        if (empty($parameters)
            || (
                !empty($parameters['cacheCmd'])
                && GeneralUtility::inList('all,system', $parameters['cacheCmd'])
                && isset($GLOBALS['BE_USER'])
            )
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'] as $extensionKey => $extensionConfiguration) {
                if (!isset($extensionConfiguration['extender']) || !is_array($extensionConfiguration['extender'])) {
                    continue;
                }

                foreach ($extensionConfiguration['extender'] as $entity => $entityConfiguration) {
                    // Get the file to extend, this needs to be loaded as first
                    $path = $this->composerClassLoader->findFile($entity);
                    if (!is_file($path)) {
                        throw new \Evoweb\Extender\Exception\FileNotFoundException(
                            'Base file "' . $path . '" does not exist'
                        );
                    }
                    $code = $this->parseSingleFile($path, false);

                    // Get the files from all other extensions that are extending this domain model
                    if (is_array($entityConfiguration)) {
                        foreach ($entityConfiguration as $extendingExtension => $extendingFilePath) {
                            $path = GeneralUtility::getFileAbsFileName($extendingFilePath);
                            if (!is_file($path) && !is_numeric($extendingExtension)) {
                                $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(
                                    $extendingExtension
                                ) . 'Classes/Extending/' . $extendingFilePath . '.php';
                            }
                            if (!is_file($path)) {
                                throw new \Evoweb\Extender\Exception\FileNotFoundException(
                                    'Extending file "' . $path . '" does not exist'
                                );
                            }
                            $code .= $this->parseSingleFile($path);
                        }
                    }

                    if (count($this->constructorLines)) {
                        $code .= implode(LF, $this->constructorLines) . LF . '    }' . LF;
                        // reset constructor lines
                        $this->constructorLines = [];
                    }

                    // Close the class definition
                    $code = $this->closeClassDefinition($code);

                    // Add the new file to the class cache
                    $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase($extensionKey) . '_' .
                        str_replace('\\', '_', $entity);
                    $this->getClassCache()->set($cacheEntryIdentifier, $code);
                }
            }
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface|\TYPO3\CMS\Core\Cache\Frontend\PhpFrontend|null
     */
    protected function getClassCache(): \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
    {
        if (is_null($this->classCache)) {
            $this->classCache = $this->cacheManager->getCache('extender');
        }
        return $this->classCache;
    }

    /**
     * Parse a single file and does some magic
     * - Remove the php tags
     * - Remove the class definition (if set)
     *
     * @param string $filePath path of the file
     * @param boolean $removeClassDefinition If class definition should be removed
     *
     * @return string path of the saved file
     * @throws \InvalidArgumentException
     */
    protected function parseSingleFile($filePath, $removeClassDefinition = true)
    {
        $code = GeneralUtility::getUrl($filePath);

        return $this->changeCode($code, $filePath, $removeClassDefinition);
    }

    /**
     * Strip php file parts that should not be used in the concatenation
     *
     * @param string $code
     * @param string $filePath
     * @param boolean $removeClassDefinition
     * @param boolean $renderPartialInfo
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function changeCode($code, $filePath, $removeClassDefinition = true, $renderPartialInfo = true)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException(sprintf('File "%s" could not be fetched or is empty', $filePath));
        }

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

        // unset the constructor and save it's lines
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
    protected function getPartialInfo($filePath)
    {
        $comment = [];
        $comment[] = '/' . str_repeat('*', 71);
        $comment[] = ' * this is partial from:';
        $comment[] = ' *  ' . str_replace(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/', '', $filePath);
        $comment[] = str_repeat('*', 71) . '/' . LF;

        return implode(LF, $comment);
    }

    /**
     * Add curly brace at the end of the class
     *
     * @param string $code
     *
     * @return string
     */
    protected function closeClassDefinition($code)
    {
        return $code . LF . '}';
    }
}
