<?php
namespace Evoweb\Extender\Utility;
/**
 * (c) 2014 Sebastian Fischer <typo3@evoweb.de>
 *
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core;

/**
 * Class ClassLoader
 */
class ClassLoader extends Core\ClassLoader {
	/**
	 * Register instance of this class as spl autoloader
	 *
	 * @return void
	 */
	public static function registerAutoloader() {
		/** @var \TYPO3\CMS\Core\Core\Bootstrap $bootstrap */
		$bootstrap = Core\Bootstrap::getInstance();
		/** @var \TYPO3\CMS\Core\Core\ClassAliasMap $classAliasMap */
		$classAliasMap = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Core\\ClassAliasMap');

		$classLoader = new self($bootstrap->getApplicationContext());
		$classLoader->injectClassAliasMap($classAliasMap);

		spl_autoload_register(array($classLoader, 'loadClass'), TRUE, TRUE);
	}

	/**
	 * Initialize cache
	 *
	 * @return \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend
	 */
	public static function initializeCache() {
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
		$cacheManager = $objectManager->get('TYPO3\\CMS\\Core\\Cache\\CacheManager');
		return $cacheManager->getCache('extender');
	}

	/**
	 * Loads php files containing classes or interfaces part of the
	 * classes directory of an extension.
	 *
	 * @param string $className Name of the class/interface to load
	 * @return boolean
	 */
	public function loadClass($className) {
		$className = ltrim($className, '\\');

		$extensionKey = $this->getExtensionKey($className);
		$classNameParts = GeneralUtility::trimExplode('\\', $className);
		$entityKey = array_pop($classNameParts);

		if (!$this->isValidClassName($className, $extensionKey, $entityKey)) {
			return FALSE;
		}

		$cacheEntryIdentifier = GeneralUtility::underscoredToLowerCamelCase($extensionKey) .
			'_' . str_replace('/', '', 'Domain/Model/' . $entityKey);

		$classCache = self::initializeCache();
		if (!$classCache->has($cacheEntryIdentifier)) {
			/** @var \Evoweb\Extender\Utility\ClassCacheManager $classCacheManager */
			$classCacheManager = GeneralUtility::makeInstance('ClassCacheManager');
			$classCacheManager->reBuild();
		}
		$classCache->requireOnce($cacheEntryIdentifier);

		return TRUE;
	}

	/**
	 * Get extension key from namespaced classname
	 *
	 * @param string $className
	 * @return string
	 */
	protected function getExtensionKey($className) {
		$extensionKey = NULL;

		if (strpos($className, '\\') !== FALSE) {
			$namespaceParts = GeneralUtility::trimExplode('\\', $className, 0, (substr($className, 0, 9) === 'TYPO3\\CMS' ? 4 : 3));
			array_pop($namespaceParts);
			$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored(array_pop($namespaceParts));
		}

		return $extensionKey;
	}

	/**
	 * Find out if a class name is valid
	 *
	 * @param string $className
	 * @param string $extensionKey
	 * @param string $entityKey
	 * @return bool
	 */
	protected function isValidClassName($className, $extensionKey, $entityKey) {
		$oldClassnamePart = substr(strtolower($className), 0, 3);
		$extensionConfiguration = isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]) ?
			(array) $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey] :
			array();

		return parent::isValidClassName($className)
			&& (
				strpos($oldClassnamePart, 'tx_') === FALSE
				&& strpos($oldClassnamePart, 'ux_') === FALSE
				&& strpos($oldClassnamePart, 'user_') === FALSE
			)
			&& (
				isset($extensionConfiguration['extender'])
				&& is_array($extensionConfiguration['extender'])
				&& isset($extensionConfiguration['extender'][$entityKey])
			);
	}
}