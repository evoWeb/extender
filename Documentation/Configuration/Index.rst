.. include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============


Configure the extend of a class
-------------------------------

Since version 10.0.0 the registration of class extends needs to be configured in
services.yaml like in this example.

.. code-block:: yaml
   :caption: Services.yaml

    services:

      Fixture\ExtendingExtension\Domain\Model\BlobExtend:
        tags:
          -
            name: 'extender.extends'
            class: Fixture\BaseExtension\Domain\Model\Blob


* Fixture\BaseExtension\Domain\Model\Blob is the class that should be extended
* Fixture\ExtendingExtension\Domain\Model\BlobExtend is the class that extends
