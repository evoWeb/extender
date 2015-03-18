.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Installation
============


Download from TER
-----------------

It's possible to download the extension from http://typo3.org/extensions/repository/view/extender
Afterwards upload the extension .t3x in the Extension Manager and install it.


Download via Extension Manager
------------------------------

In the TYPO3 Backend go to Admin Tools > Extension Manager. Change in the dropdown on the top left
to 'Get Extensions', enter the extension key 'extender' in the text field below the headline 'Get
Extensions' and hit go. In the result list install the extension by hitting the action for that.


Download via Composer
---------------------

Add evoweb/extender to the require in your composer.json like in the following example and run 'composer install'

::

	{
		"require": {
			"typo3/cms": "~6.2",
			"evoweb/extender": "*",
		}
	}


Alternatively if you have an existing project with a configured composer.json you can add extender with
the command by running 'composer require evoweb/extender'