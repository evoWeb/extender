.. include:: ../Includes.txt

.. _configuration:

=============
Configuration
=============


Configure the extend of a class
-------------------------------

Basically extending is very simple from the configuration perspective.
It consist of a line to be added to the ext_localconf.php in the extension
that wants to extend a class.

::

	$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['EXTKEY1']['extender']['CLASSNAME']['EXTKEY2'] =
		\Vendor\EXTKEY2\Domain\Model\CLASSNAME::class;

	$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['EXTKEY1']['extender']['CLASSNAME']['EXTKEY2'] =
		'EXT:EXTKEY2/Classes/Domain/Model/CLASSNAME.php';

	$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['EXTKEY1']['extender']['CLASSNAME']['EXTKEY2'] =
		'Domain/Model/SingleModel';


* EXTKEY1 - extension key of the extension in which the class should be extended
* EXTKEY2 - extension key of the extension in which the extending class resides
* CLASSNAME - classname of the class to be extended including the complete namespace
* RECOMMENDED
  Assigned entity classname extending the base entity
  Example: \Vendor\NewsExtended\Domain\Model\MyExtendingModel::class
  Result: Environment::getPublicPath() . '/typo3conf/ext/news_extended/Classes/Domain/Model/MyExtendingModel.php'
* Assigned path - either a complete path prepended with EXT: to the file relative from typo3conf/ext
  Example: 'EXT:news_extended/Classes/Domain/Model/MyExtendingModel.php'
  Result: Environment::getPublicPath() . '/typo3conf/ext/news_extended/Classes/Domain/Model/MyExtendingModel.php'
* Assigned path - or a relative path to the extending extensions. This will be prepended with Classes/Extending/ and appended with .php
  Example: 'Domain/Model/MyExtendingModel'
  Result: Environment::getPublicPath() . '/typo3conf/ext/news_extended/Classes/Extending/Domain/Model/MyExtendingModel.php'

Because extbase is very strict in the coding guideline that's basically it. As
long as the class are found in the corresponding paths and the classname equals
the filename nothing else need to be configured.

The second extension key is not required but advised to prevent colliding multiple
extends of one class.

::

	$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['store_finder']['extender'][
		\Evoweb\StoreFinder\Domain\Model\Location::class
	]['sitepackage'] = \Evoweb\Sitepackage\Domain\Model\Location::class;

	$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['store_finder']['extender'][
		\Evoweb\StoreFinder\Domain\Model\Location::class
	]['sitepackage'] = 'EXT:sitepackage/Classes/Extending/Domain/Model/Location.php';

	$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['store_finder']['extender'][
		\Evoweb\StoreFinder\Domain\Model\Location::class
	]['sitepackage'] = 'Domain/Model/Location';

In this example the store_finder is the extension and \Evoweb\StoreFinder\Domain\Model\Location
the class to extend. Concluding that sitepackage is the extension and
\Evoweb\Sitepackage\Domain\Model\Location the class extending it.
