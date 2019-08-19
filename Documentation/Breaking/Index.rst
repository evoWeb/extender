.. include:: ../Includes.txt

.. _breaking-change:

=====================================
Breaking change to register extending
=====================================


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
