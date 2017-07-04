<?php
namespace Evoweb\Extender\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class \Evoweb\Extender\Utility\ClassCacheManager
 *
 * @author Sebastian Fischer <typo3@evoweb.de>
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
     * @var \Composer\Autoload\ClassLoader
     */
    protected $composerClassLoader;

    /**
     * @var array
     */
    protected $constructorLines = array();

    /**
     * Constructor
     *
     * @param \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $classCache
     */
    public function __construct($classCache = null)
    {
        if (is_null($classCache)) {
            /**
             * Cache manager
             *
             * @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager
             */
            $cacheManager = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class);
            // Set configuration in case some cache settings are not loaded by now.
            $cacheManager->setCacheConfigurations($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']);
            /** @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend $cache */
            $classCache = $cacheManager->getCache('extender');
        }

        $this->classCache = $classCache;
        $this->composerClassLoader = \TYPO3\CMS\Core\Core\Bootstrap::getInstance()
            ->getEarlyInstance(\Composer\Autoload\ClassLoader::class);
    }

    /**
     * Rebuild the class cache
     *
     * @param array $parameters
     *
     * @throws \Evoweb\Extender\Exception\FileNotFoundException
     * @throws \TYPO3\CMS\Core\Cache\Exception\InvalidDataException
     * @return void
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
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'] as $extensionKey => $extensionConfiguration) {
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
                        foreach ($entityConfiguration as $extendingExtension => $extendingFilepath) {
                            $path = GeneralUtility::getFileAbsFileName($extendingFilepath);
                            if (!is_file($path) && !is_numeric($extendingExtension)) {
                                $path = ExtensionManagementUtility::extPath($extendingExtension) .
                                    'Classes/Extending/' . $extendingFilepath . '.php';
                            }
                            if (!is_file($path)) {
                                throw new \Evoweb\Extender\Exception\FileNotFoundException(
                                    'Extending file "' . $path . '" does not exist'
                                );
                            }
                            $code .= $this->parseSingleFile($path);
                        }
                    }

                    if (!empty($this->constructorLines)) {
                        $code .= '    public function __construct()' . LF . '    {' . LF. implode(LF, $this->constructorLines) . LF . '    }' . LF;
                    }

                    // Close the class definition
                    $code = $this->closeClassDefinition($code);

                    // Add the new file to the class cache
                    $cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase($extensionKey) . '_' .
                        str_replace('\\', '_', $entity);
                    $this->classCache->set($cacheEntryIdentifier, $code);
                }
            }
        }
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
     * Strip php file parts that should not be used in the concatination
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
        $code = trim($code);
        $code = str_replace(array('<?php', '?>'), '', $code);
        $code = trim($code);

        // Remove everything before 'class ', including namespaces,
        // comments and require-statements.
        if ($removeClassDefinition) {
            $pos = strpos($code, 'class ');
            $pos2 = strpos($code, '{', $pos);

            $code = substr($code, $pos2 + 1);
        }

        $contructorPosition = strpos($code, '   public function __construct');
        if ($contructorPosition !== false) {
            $contructorCodePositionStart = strpos($code, '{', $contructorPosition) + 1;
            $contructorCodePositionEnd = strpos($code, '    }', $contructorCodePositionStart) - 1;
            $contructorCode = substr($code, $contructorCodePositionStart, $contructorCodePositionEnd - $contructorCodePositionStart);
            $this->constructorLines[] = trim($contructorCode, "\n\r");

            $contructorCodeComplete = substr($code, $contructorPosition - 1, $contructorCodePositionEnd + 7 - ($contructorPosition - 1));

            $code = str_replace($contructorCodeComplete, '', $code);
        }


        $code = trim($code);

        // Add some information for each partial
        if ($renderPartialInfo) {
            $code = $this->getPartialInfo($filePath) . $code;
        }

        // Remove last }
        $pos = strrpos($code, '}');
        $code = substr($code, 0, $pos);
        $code = trim($code);

        return $code . LF . LF;
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
        return '/' . str_repeat('*', 71) . '
 * this is partial from:
 *  ' . str_replace(PATH_site, '', $filePath) . LF . str_repeat('*', 71) . '/
    ';
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
