<?php
namespace Evoweb\Extender\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Sebastian Fischer <typo3@evoweb.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ClassCacheManager
 */
class ClassCacheManager {
	/**
	 * @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend
	 */
	protected $cacheInstance;

	/**
	 * Constructor
	 *
	 * @return self
	 */
	public function __construct() {
		$this->cacheInstance = ClassLoader::initializeCache();
	}

	/**
	 * Rebuild the class cache
	 *
	 * @param array $parameters
	 * @return void
	 */
	public function reBuild(array $parameters = array()) {
		if (
			empty($parameters)
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
					$key = 'Domain/Model/' . $entity;

					// Get the file from sf_register itself, this needs to be loaded as first
					$path = ExtensionManagementUtility::extPath($extensionKey) . 'Classes/' . $key . '.php';
					if (!is_file($path)) {
						throw new \Exception('given file "' . $path . '" does not exist');
					}
					$code = $this->parseSingleFile($path, FALSE);

					// Get the files from all other extensions that are extending this domain model class
					if (is_array($entityConfiguration)) {
						foreach ($entityConfiguration as $extendingExtension => $extendingFilepath) {
							$path = GeneralUtility::getFileAbsFileName($extendingFilepath, FALSE);
							if (!is_file($path) && !is_numeric($extendingExtension)) {
								$path = ExtensionManagementUtility::extPath($extendingExtension) . 'Classes/' . $key . '.php';
							}
							$code .= $this->parseSingleFile($path);
						}
					}

						// Close the class definition
					$code = $this->closeClassDefinition($code);

						// Add the new file to the class cache
					$cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase($extensionKey) . '_' . str_replace('/', '', $key);
					$this->cacheInstance->set($cacheEntryIdentifier, $code);
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
	 * @return string path of the saved file
	 * @throws \InvalidArgumentException
	 */
	public function parseSingleFile($filePath, $removeClassDefinition = TRUE) {
		if (!is_file($filePath)) {
			throw new \InvalidArgumentException(sprintf('File "%s" could not be found', $filePath));
		}
		$code = GeneralUtility::getUrl($filePath);
		return $this->changeCode($code, $filePath, $removeClassDefinition);
	}

	/**
	 * @param string $code
	 * @param string $filePath
	 * @param boolean $removeClassDefinition
	 * @param boolean $renderPartialInfo
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	protected function changeCode($code, $filePath, $removeClassDefinition = TRUE, $renderPartialInfo = TRUE) {
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
	 * @param string $filePath
	 * @return string
	 */
	protected function getPartialInfo($filePath) {
		return '/' . str_repeat('*', 71) . '
 * this is partial from: ' . $filePath . LF . str_repeat('*', 71) . '/
	';
	}

	/**
	 * @param string $code
	 * @return string
	 */
	protected function closeClassDefinition($code) {
		return $code . LF . '}';
	}
}