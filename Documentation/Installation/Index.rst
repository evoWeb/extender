..  include:: /Includes.rst.txt
..  index:: Installation
..  _installation:

============
Installation
============

As EXT:extender is based on composer and its mechanisms the installation is only
possible with composer methods.

**WARNING** In previous versions it was possible to use extender when installed
via the Extension Manager. This is not possible anymore.

Require via command
===================

You can add extender with composer require.

..  code-block:: bash
    composer require evoweb/extender

Modify composer.json
====================

Additionally evoweb/extender can be added to the require in your composer.json,
like in the following example and run 'composer install'.

..  code-block:: json
    :caption: composer.json

    {
        "require": {
            ...
            "evoweb/extender": "*"
        }
    }
