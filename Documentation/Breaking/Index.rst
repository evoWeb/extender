.. include:: ../Includes.txt

.. _breaking-change:

=====================================
Breaking change to register extending
=====================================

Changed registering class loader
________________________________

Description
-----------

Since 7.1.0 the class loader gets registered by an EventListener

Impact
------

Normally the class loader does not get registered other then EXT:extender,
but in case you need to have the class loader registered earlier, the following
calls needs to be replaced:

Old and discouraged
::

	\Evoweb\Extender\Utility\ClassLoader::registerAutoloader();

Since 7.1.0
::

	$event = new \Evoweb\Extender\Utility\Event\ClassLoaderEvent();
	/** @var \TYPO3\CMS\Core\EventDispatcher\EventDispatcher $eventDispatcher */
	$eventDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
		\TYPO3\CMS\Core\EventDispatcher\EventDispatcher::class
	);
	$eventDispatcher->dispatch($event);


Changed configuration
_____________________

Description
-----------

Since version 7.0.0 all usage of EXTCONF is replaced with EXTENSIONS.


Impact
------

All class extending still using EXTCONF to not work anymore. So the code still
fills the array but this array is not used anymore.


Affected Installations
----------------------

All extensions that use EXTCONF in registration of class extending like.

::

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['store_finder']['extender'][
		\Evoweb\StoreFinder\Domain\Model\Location::class
	]['sitepackage'] = 'EXT:sitepackage/Classes/Domain/Model/Location.php';


Migration
---------

Replace the usage of EXTCONF with EXTENSIONS to have the class extended again.
