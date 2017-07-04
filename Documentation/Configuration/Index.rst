.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Configuration
=============


Configure the extend of a domain model
--------------------------------------

Basicly extending is very simple from the configuration perspective.
It consist of a line to be added to the ext_localconf.php in the extension
that wants to extend a domain model.

::

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['EXTKEY1']['extender']['CLASSNAME']['EXTKEY2'] =
		'EXT:EXTKEY2/Classes/Domain/Model/CLASSNAME.php';
		
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['EXTKEY1']['extender']['CLASSNAME']['EXTKEY2'] =
		'Domain/Model/SingleModel';


* EXTKEY1 - extension key of the extension in which the domain model should be extended
* EXTKEY2 - extension key of the extension in which the extending domain model resides
* CLASSNAME - classname of the domain model to be extended including the complete namespace
* Assigned path - either a complete path prepended with EXT: to the file relative from typo3conf/ext  
  Example: 'EXT:news_extended/Classes/Domain/Model/MyExtendingModell.php'  
  Result: PATH_site . 'typo3conf/ext/news_extended/Classes/Domain/Model/MyExtendingModell.php'
* Assigned path - or a relative path to the extending extensions. This will be prepended with Classes/Extending/ and appended with .php  
  Example: 'Domain/Model/MyExtendingModell'  
  Result: PATH_site . 'typo3conf/ext/news_extended/Classes/Extending/Domain/Model/MyExtendingModell.php'

Because extbase is very strict in the coding guideline thats basicly it. As long as
the domain model are found in the corresponding pathes and the classname equals the
filename nothing else need to be configured.

The second extension key is not required but advised to prevent colliding multiple
extends of one domain model.
