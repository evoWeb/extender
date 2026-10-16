---
title: "Installation"
manual: "extender"
version: "11.1.3"
permalink: "https://docs.typo3.org/permalink/evoweb/extender:installation@11.1.3"
source: "Installation/Index.rst"
rendered: "2026-10-16T14:34:39+00:00"
---

# Installation {#installation}

As EXT:extender is based on composer and its mechanisms the installation is only
possible with composer methods.

**WARNING** In previous versions it was possible to use extender when installed
via the Extension Manager. This is not possible anymore.

## Require via command {#require-via-command}

You can add extender with composer require.

```bash
composer require evoweb/extender
```

## Modify composer.json {#modify-composer-json}

Additionally evoweb/extender can be added to the require in your composer.json,
like in the following example and run 'composer install'.

**composer.json**

```json
{
    "require": {
        ...
        "evoweb/extender": "*"
    }
}
```
