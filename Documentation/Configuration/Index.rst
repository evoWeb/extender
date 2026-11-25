..  include:: /Includes.rst.txt
..  index:: Configuration
..  _configuration:

=============
Configuration
=============

..  contents:: Table of contents
    :local:
    :depth: 1

..  _configuration-register:

Register the extending class
============================

Since version 10.0.0 the registration of class extends needs to be configured
in the :file:`Configuration/Services.yaml` of the extending extension.

The extending class is registered as a service and gets the tag
`extender.extends`. The attribute `class` of the tag contains the class that
should be extended:

*   `Fixture\BaseExtension\Domain\Model\Blob` is the class that should be
    extended (base class).
*   `Fixture\ExtendingExtension\Domain\Model\BlobExtend` is the class that
    extends the base class (extending class).

..  code-block:: yaml
    :caption: EXT:extending_extension/Configuration/Services.yaml

    services:
      Fixture\ExtendingExtension\Domain\Model\BlobExtend:
        tags:
          -
            name: 'extender.extends'
            class: Fixture\BaseExtension\Domain\Model\Blob

This minimal registration works, but it should be combined with the settings
described in the next section.

..  _configuration-container-compilation:

Prevent loading the base class during container compilation
===========================================================

The class loader of extender is registered when the event dispatcher service
is instantiated, that is only after the dependency injection container exists.
While the container is being compiled, the class loader of extender is not
registered yet. Any class that is loaded during the compilation is loaded by
the Composer class loader, which means: the original base class **without**
the code of the extending class.

PHP cannot load a class twice. The process that compiled the container keeps
working with the original base class until it ends.

..  _configuration-container-compilation-extending-class:

The extending class is reflected by Symfony
-------------------------------------------

The service definition of the extending class inherits the `_defaults` of the
:file:`Services.yaml`, typically `autowire: true` and `autoconfigure: true`.

*   With `autoconfigure: true`, Symfony reflects the class to search for
    attributes (`RegisterAutoconfigureAttributesPass`).
*   With `autowire: true`, Symfony reflects the class to resolve constructor
    arguments (`AutowirePass`).

As the extending class `extends` the base class, reflecting the extending
class makes PHP autoload the base class through Composer.

Excluding the folder of the extending class (for example
:file:`Classes/Domain/Model/`) from the `resource` scan does **not** help.
The explicit service definition, which is needed for the tag, is independent
of the resource scan and still inherits `_defaults`.

Disable `autowire` and `autoconfigure` in the definition of the extending
class:

..  code-block:: yaml
    :caption: EXT:extending_extension/Configuration/Services.yaml

    services:
      _defaults:
        autowire: true
        autoconfigure: true
        public: false

      Fixture\ExtendingExtension\Domain\Model\BlobExtend:
        # Prevent reflection, which would autoload the base class too early.
        autowire: false
        autoconfigure: false
        tags:
          -
            name: 'extender.extends'
            class: Fixture\BaseExtension\Domain\Model\Blob

..  _configuration-container-compilation-public-base-class:

The extended extension registers the base class as public service
-----------------------------------------------------------------

Some extensions register their own domain model as a public service, for
example to use it in their tests. While dumping the container, Symfony loads
the classes of such definitions to collect the class lineage for preloading.
This happens even if `autowire` and `autoconfigure` are disabled.

Override this definition in the :file:`Services.yaml` of the extending
extension with `public: false`, `autowire: false` and `autoconfigure: false`.
The override only takes effect if the :file:`Services.yaml` of the extending
extension is loaded **after** the one of the extended extension. Therefore,
the extending extension must depend on the extended extension (in
:file:`composer.json` and :file:`ext_emconf.php`).

An unused private service definition is removed from the container.
:php:`GeneralUtility::makeInstance()` then falls back to `new`, which still
uses the class loader of extender and returns the extended class.

..  code-block:: yaml
    :caption: EXT:extending_extension/Configuration/Services.yaml

    services:
      Fixture\BaseExtension\Domain\Model\Blob:
        # Override the public definition of the extended extension.
        public: false
        autowire: false
        autoconfigure: false

..  _configuration-container-compilation-consequences:

Consequences of a base class loaded too early
---------------------------------------------

If the process that compiled the container is a frontend request that uses
Extbase with this model (for example a list or detail view), Extbase builds
the class schema and the data map from the original base class. The data map
is stored in the shared Extbase cache.

All later requests use the correct extended class, but the broken data map:
properties of the extending class are missing or relations are not detected.
Typical errors are:

*   `Call to undefined method Fixture\BaseExtension\Domain\Model\Blob::getOtherProperty()`
    (a method of the extending class) in the request that compiled the
    container.
*   `TypeError: DataMapper::thawObjectProperty(): Return value must be of type
    ?object, array returned` in later requests, for example if the extending
    class adds an empty to-one relation.

..  warning::
    The broken data map persists until the Extbase cache is flushed again.
    Configure the services as described above to make sure that all classes
    are loaded through the class loader of extender, even in the process that
    compiles the container.

..  _configuration-cache-flush:

Cache flushes and concurrent requests
=====================================

After a full cache flush it matters which process compiles the container.
The following recommendations reduce the risk that the base class is loaded
during container compilation of a frontend request.

..  note::
    A cache flush in the backend, for example with
    :guilabel:`Flush TYPO3 and PHP Cache` in the :guilabel:`Maintenance`
    module, is usually less problematic: the next backend request of the same
    editor rebuilds the container right away and does not render frontend
    Extbase plugins. This is a matter of favorable timing, not a guarantee.

..  warning::
    The command :bash:`typo3 cache:flush` removes the container, but no
    request rebuilds it until :bash:`typo3 cache:warmup` has finished. In
    between, every concurrent frontend request finds no container and
    compiles one itself, as there is no lock around the container
    compilation. On busy sites this is exactly the time window in which the
    base class can end up in the Extbase cache.

..  important::
    In deployments, run :bash:`typo3 cache:flush` and
    :bash:`typo3 cache:warmup` for the new release **before** switching the
    release symlink. This way live traffic never hits an empty container.

    This is only recommended if the caches of the new release do not contain
    paths that change with the symlink switch. Some setups store absolute or
    resolved real paths in caches. Check your setup before applying this.

The class loader of extender itself handles concurrent flushes of its own
cache: if a concurrent cache flush removes a generated proxy file between the
check for its existence and loading it, the class loader rebuilds the proxy
file instead of failing.
