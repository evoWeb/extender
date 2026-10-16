---
title: "Configuration"
manual: "extender"
version: "11.1.3"
permalink: "https://docs.typo3.org/permalink/evoweb/extender:configuration@11.1.3"
source: "Configuration/Index.rst"
rendered: "2026-10-16T14:34:39+00:00"
---

# Configuration {#configuration}

## Configure the extend of a class {#configure-the-extend-of-a-class}

Since version 10.0.0 the registration of class extends needs to be configured in
services.yaml like in this example.

**Services.yaml**

```yaml
services:
  Fixture\ExtendingExtension\Domain\Model\BlobExtend:
    tags:
      -
        name: 'extender.extends'
        class: Fixture\BaseExtension\Domain\Model\Blob
```

-   FixtureBaseExtensionDomainModelBlob is the class that should be extended
-   FixtureExtendingExtensionDomainModelBlobExtend is the class that extends
