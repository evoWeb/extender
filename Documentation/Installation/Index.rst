.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============


Download via Extension Manager
------------------------------

In the TYPO3 Backend go to Admin Tools > Extensions. Change in the dropdown on
the top left to 'Get Extensions', enter the extension key 'extender' in the text
field below the headline 'Get Extensions' and hit go. In the result list install
the extension by hitting the action for that.


Download via Composer
---------------------

Add evoweb/extender to the require in your composer.json like in the following
example and run 'composer install'.

.. code-block:: json
   :caption: composer.json

	{
		"require": {
			"typo3/cms-core": "^10.0",
			"evoweb/extender": "*",
		}
	}


Alternatively if you have an existing project with a configured composer.json you
can add extender with the command by running 'composer require evoweb/extender'.
