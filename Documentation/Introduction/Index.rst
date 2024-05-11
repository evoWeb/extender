.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============


This Documentation was written for version 7.0.x of the extension.


Extending classes
-----------------

The sole purpose of this extension is to fill a gap that extbase leaves open.

As it is not possible to extend classes in extbase directly its sometimes not
possible to add properties and methods to a class. This is valid for all cases
where the class is given as an type hint to an action.

Arguments of an action are mapped from the request to the concrete class by
the argument mapper. The argument mapper does not take TypoScript object
className mapping into account as the property mapper. Due to this its not
possible to just extend the class and have the extends available.

To cope with this and be able to add custom properties to a class the extender
registers a custom spl class loader. This kicks in for every configured class
and required a compiled class from the class cache.

The class cache gets generated on every hit where an configured compilation is
not available and after every clear system cache. In both cases the class cache
manager rebuilds the complete class cache.

As the class cache is registered to the system cache group it gets cleared on
every clear system cache or clear all caches. After that a hook gets called that
rebuilds the class cache. Unless a huge amount of extends are configured there
should be a prefilled class cache on every request.


Deep dive into an example
-------------------------

The cache manager parses the base and extend files to gather the colored parts
of the following images, generates a combined file as shown in the merged result
and adds it to the cache.


.. rst-class:: bignums

#. Base class file

   .. figure:: Images/base.png

#. Extend class file

   .. figure:: Images/extend.png

#. Merged result file

   .. figure:: Images/merged.png

Explanation
-----------

..  rst-class:: bignums-attention

   #. Namespace

      The Namespace is taken from the base file. The namespace of the extended
      file is ignored.

   #. Uses

      All uses from base and extending file are taken uniquely. If an use appears
      with diverting as alias it is present twice in the merged file.

      .. code-block::
        :caption: example of uses appearing twice

        use Psr\Log\LoggerAwareTrait;
        use Psr\Log\LoggerAwareTrait as T;

   #. Class

      The class name and the extends part is taken from the base class.

   #. Implements

      Implements are used uniquely from the base and extend file

   #. Traits

      All traits from base and extend file are taken uniquely.

   #. Properties

      All properties from base and extend file are taken without check if they
      are not colliding.

   #. Construct

      The __construct of base and extend file are taken with merged contents and
      arguments. Where arguments from base take priority.
      All line of code in the method are taken. If the __construct of the extend
      file contains a parent::__construct call it gets removed.

   #. Methods

      All methods beside __construct from base and extend file are taken without
      check if they are not colliding.

   #. Comment

      The comment is based on the base and extending files and display which
      files path were taken into account.

Example source
--------------

The base file content could be found in
EXT:extender/Tests/Fixtures/Extensions/base_extension/Classes/Domain/Model/Blob.php

The extend file content is derived from
EXT:extender/Tests/Fixtures/Extensions/extending_extension/Classes/Domain/Model/BlobExtend.php

Important
---------
As in both files shown, it's important to use the FQCN to extend of, else the
usage of the class gets written to the merged file and result in two classes
with the same name in the cache file.

.. code-block:: php
   :caption: Correct extension

    namespace Fixture\ExtendingClass\Domain\Model;

    class ExtendingModel extends \Fixture\BaseClass\Domain\Model\BaseModel
    {
    }

.. code-block:: php
   :caption: Result with correct extension

    namespace Fixture\ExtendingClass\Domain\Model;

    class BaseModel
    {
    }

While linting the file will not raise an error and the class is usable,
it will definitely irritate editors like PHPStorm or Visual Code.

.. code-block:: php
   :caption: Wrong extension

    namespace Fixture\ExtendingClass\Domain\Model;

    use Fixture\BaseClass\Domain\Model\BaseModel;

    class ExtendingModel extends BaseModel
    {
    }

.. code-block:: php
   :caption: Result with wrong extension

    namespace Fixture\BaseClass\Domain\Model;

    use Fixture\BaseClass\Domain\Model\BaseModel;

    class BaseModel
    {
    }
