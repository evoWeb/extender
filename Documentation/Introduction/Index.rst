.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt
.. include:: Images.txt

Introduction
============

This Documentation was written for version 6.0.x of the extension.


What does it do?
----------------


Extending extbase domain models:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The sole purpose of this extension is to fill a gap that extbase leaves open.

As it is not possible to extend domain models in extbase directly its sometimes
not possible to add properties and methods to a model. This is valid for all
cases where the domain model is given as an type hint to an action.

Arguments of an action are mapped from the request to the concret domain model
by the argument mapper. The argument mapper does not take typoscript object
className mapping into account as the property mapper. Due to this its not
possible to just extend the domain model and have the extends available.

To cope with this and be able to add custom properties to a domain model the
extender registers a custom spl class loader. This kicks in for every configured
domain model and required a compiled domain model from the class cache.

The class cache gets generated on every hit where an configured compilation is
not available and after every clear system cache. In both cases the class cache
manager rebuilds the complete class cache.

As the class cache is registed to the system cache group it gets cleared on every
clear system cache or clear all caches. After that a hook gets called that
rebuilds the class cache. Unless a huge amount of extends are configured there
should be a prefilled class cache on every request.
