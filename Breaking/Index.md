---
title: "Breaking change"
manual: "extender"
version: "11.1.3"
permalink: "https://docs.typo3.org/permalink/evoweb/extender:breaking-changes@11.1.3"
source: "Breaking/Index.rst"
rendered: "2026-10-16T14:34:39+00:00"
---

# Breaking change {#breaking-changes}

## Change of command name {#change-of-command-name}

The command was renamed from "extender:rebuild" to "extender:clearClassCache"
to better reflect what the command is doing.

## Change of extending configuration in 10.0.0 {#change-of-extending-configuration-in-10-0-0}

### Description {#description}

Since version 10.0.0 the registration happens in services.yaml

### Impact {#impact}

All class extending in ext_localconf.php needs to be replaced and converted

### Migration {#migration}

Migrate configuration from array to yaml.

**before ext_localconf.php**

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['base_extension']['extender'][
    \Fixture\BaseExtension\Domain\Model\Blob::class
]['extending_extension'] = 'EXT:extending_extension/Classes/Domain/Model/BlobExtend.php';
```

**after Services.yaml**

```yaml
Fixture\ExtendingExtension\Domain\Model\BlobExtend:
  tags:
    -
      name: 'extender.extends'
      class: Fixture\BaseExtension\Domain\Model\Blob
```

## Change of extending configuration in 7.0.0 {#change-of-extending-configuration-in-7-0-0}

### Description {#description-1}

Since version 7.0.0 all usage of EXTCONF is replaced with EXTENSIONS.

### Impact {#impact-1}

All class extending still using EXTCONF to not work anymore. So the code still
fills the array but this array is not used anymore.

### Affected Installations {#affected-installations}

All extensions that use EXTCONF in registration of class extending like.

**before ext_localconf.php**

```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['store_finder']['extender'][
    \Evoweb\StoreFinder\Domain\Model\Location::class
]['sitepackage'] = 'EXT:sitepackage/Classes/Domain/Model/Location.php';
```

### Migration {#migration-1}

Replace the usage of EXTCONF with EXTENSIONS to have the class extended again.
